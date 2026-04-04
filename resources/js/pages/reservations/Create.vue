<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { loadStripe } from '@stripe/stripe-js';
import { onMounted, ref } from 'vue';
import { route } from 'ziggy-js';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';


const props = defineProps<{
    room: {
        id: number;
        number: string;
        capacity: number;
        price_cents: number;
    };
    auth: {
        user: {
            name: string;
        };
    };
}>();

const stripeKey = import.meta.env.VITE_STRIPE_KEY;
let stripe: any = null;
let elements: any = null;
let cardElement: any = null;

const form = useForm({
    room_id: props.room.id,
    accompany_number: 0,
    stripe_token: '',
});

const cardError = ref<string | null>(null);
const isProcessing = ref(false);
const isStripeReady = ref(false);

onMounted(async () => {
    if (!stripeKey) {
        cardError.value = 'Payment is temporarily unavailable. Missing Stripe publishable key.';

        return;
    }

    try {
        stripe = await loadStripe(stripeKey);

        if (!stripe) {
            cardError.value = 'Unable to initialize Stripe. Please try again later.';

            return;
        }

        elements = stripe.elements();
        cardElement = elements.create('card');
        cardElement.mount('#card-element');
        cardElement.on('change', (event: any) => {
            cardError.value = event.error ? event.error.message : null;
        });

        isStripeReady.value = true;
    } catch {
        cardError.value = 'Unable to initialize payment form. Please refresh the page.';
    }
});

const submit = async () => {
    if (!isStripeReady.value || !stripe || !cardElement) {
        cardError.value = 'Payment form is not ready yet. Please refresh and try again.';

        return;
    }

    isProcessing.value = true;
    const { token, error } = await stripe.createToken(cardElement);

    if (error) {
        cardError.value = error.message;
        isProcessing.value = false;

        return;
    }

    form.stripe_token = token.id;
    form.post(route('reservations.store'), {
        onFinish: () => {
            isProcessing.value = false;
        },
    });
};

const formatPrice = (cents: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(cents / 100);
};
</script>

<template>
    <Head title="Book Room" />

    <AppLayout>
        <div class="px-4 py-6 max-w-2xl mx-auto">
            <Heading title="Complete Your Reservation" :description="`You are booking Room #${room.number}`" />

            <Card class="mt-8">
                <form @submit.prevent="submit">
                    <CardHeader>
                        <CardTitle>Room Details</CardTitle>
                        <CardDescription>Price: {{ formatPrice(room.price_cents) }} / night</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div class="space-y-2">
                            <Label for="accompany_number">Number of Accompanying People (Max: {{ room.capacity }})</Label>
                            <Input
                                id="accompany_number"
                                type="number"
                                v-model="form.accompany_number"
                                :min="0"
                                :max="room.capacity"
                                required
                            />
                            <InputError :message="form.errors.accompany_number" />
                        </div>

                        <div class="space-y-2">
                            <Label>Credit or Debit Card</Label>
                            <div id="card-element" class="p-3 border rounded-md bg-background"></div>
                            <p v-if="cardError" class="text-sm text-destructive mt-1">{{ cardError }}</p>
                            <InputError :message="form.errors.stripe_token" />
                            <InputError :message="(form.errors as Record<string, string>).payment" />
                        </div>
                    </CardContent>
                    <CardFooter>
                        <Button type="submit" class="w-full" :disabled="isProcessing || form.processing || !isStripeReady">
                            {{ isProcessing ? 'Processing Payment...' : `Pay ${formatPrice(room.price_cents)}` }}
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>

<style scoped>
#card-element {
    min-height: 40px;
}
</style>
