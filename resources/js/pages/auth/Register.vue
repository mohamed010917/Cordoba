<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue'
import InputError from '@/components/InputError.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  phone: '',
  national_id: '',
  gender: '',
  country_id: '',
  image: null,
})

function submit() {
  form.post('register')
}

function handleImageChange(event) {
  form.image = event.target.files[0]
}
</script>

<template>
  <AuthLayout>
    <Head title="Register" />

    <div class="fixed inset-0 min-h-screen w-full bg-[#060b13] flex overflow-hidden font-sans z-50">
      
      <div class="w-full grid lg:grid-cols-2 h-full">
        
        <div class="hidden lg:flex flex-col justify-center p-16 lg:p-24 relative border-r border-white/5 shrink-0 h-full overflow-hidden
                    bg-[url('https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?q=80&w=1920&auto=format&fit=crop')] 
                    bg-cover bg-center">
            
            <div class="absolute inset-0 bg-gradient-to-br from-[#1a2c4e]/90 via-[#0e1726]/95 to-[#060b13]/98 z-0"></div>

            <div class="absolute -top-20 -left-20 w-96 h-96 bg-blue-500/10 rounded-full blur-[120px] z-10"></div>

            <div class="relative z-20 space-y-12 animate-slide-in-left">
                <div>
                    <h2 class="text-cyan-400 font-bold tracking-[0.2em] uppercase text-sm mb-4">The Azure Horizon</h2>
                    <h1 class="text-6xl xl:text-7xl font-extrabold text-white leading-[1.1] tracking-tight">
                        Experience <br/> 
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-300">Pure Serenity.</span>
                    </h1>
                </div>

                <div class="space-y-10 max-w-md">
                    <div class="flex items-start gap-6 group">
                        <div class="w-14 h-14 shrink-0 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center group-hover:border-cyan-500/50 transition-all duration-500">
                            <svg class="w-7 h-7 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-xl text-white font-bold mb-1">Tailored Concierge</h4>
                            <p class="text-gray-300 leading-relaxed">Personalized 24/7 support to ensure every moment of your stay is curated to perfection.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-6 group">
                        <div class="w-14 h-14 shrink-0 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center group-hover:border-cyan-500/50 transition-all duration-500">
                            <svg class="w-7 h-7 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg>
                        </div>
                        <div>
                            <h4 class="text-xl text-white font-bold mb-1">Global Access</h4>
                            <p class="text-gray-300 leading-relaxed">One membership for all our locations worldwide, from alpine peaks to tropical shores.</p>
                        </div>
                    </div>
                </div>

                <div class="pt-8 border-t border-white/5 flex items-center gap-4">
                    <div class="flex -space-x-3">
                        <div class="w-10 h-10 rounded-full border-2 border-[#0e1726] bg-gray-600"></div>
                        <div class="w-10 h-10 rounded-full border-2 border-[#0e1726] bg-gray-500"></div>
                        <div class="w-10 h-10 rounded-full border-2 border-[#0e1726] bg-gray-400"></div>
                    </div>
                    <p class="text-sm text-gray-500 font-medium">Joined by 12,000+ global travelers</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col bg-[#0e1726] h-full overflow-y-auto relative z-30">
            <div class="max-w-xl mx-auto w-full p-8 md:p-16 lg:p-20 space-y-10 animate-fade-in">
                
                <div class="space-y-2">
                    <h3 class="text-3xl font-extrabold text-white">Create your account</h3>
                    <p class="text-gray-400">Start your journey with us today.</p>
                </div>

                <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                    
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1">Full Name</label>
                        <input v-model="form.name" type="text" required class="w-full bg-[#162133] border border-white/5 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 transition-all" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1">Email</label>
                        <input v-model="form.email" type="email" required class="w-full bg-[#162133] border border-white/5 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 transition-all" />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1">Phone</label>
                        <input v-model="form.phone" type="text" class="w-full bg-[#162133] border border-white/5 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 transition-all" />
                        <InputError :message="form.errors.phone" />
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1">National ID</label>
                        <input v-model="form.national_id" type="text" class="w-full bg-[#162133] border border-white/5 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 transition-all" />
                        <InputError :message="form.errors.national_id" />
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1">Gender</label>
                        <select v-model="form.gender" required class="w-full bg-[#162133] border border-white/5 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 transition-all appearance-none">
                            <option value="">Select</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                        <InputError :message="form.errors.gender" />
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1">Country ID</label>
                        <input v-model="form.country_id" type="number" required class="w-full bg-[#162133] border border-white/5 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 transition-all" />
                        <InputError :message="form.errors.country_id" />
                    </div>

                    <div class="md:col-span-2 space-y-1">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1">Avatar</label>
                        <input type="file" @change="handleImageChange" class="w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white cursor-pointer" />
                        <InputError :message="form.errors.image" />
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1">Password</label>
                        <input v-model="form.password" type="password" required class="w-full bg-[#162133] border border-white/5 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 transition-all" />
                        <InputError :message="form.errors.password" />
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1">Confirm</label>
                        <input v-model="form.password_confirmation" type="password" required class="w-full bg-[#162133] border border-white/5 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 transition-all" />
                        <InputError :message="form.errors.password_confirmation" />
                    </div>

                    <div class="md:col-span-2 pt-6 flex flex-col gap-4">
                        <button type="submit" :disabled="form.processing" class="w-full py-4 bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-bold rounded-xl shadow-lg hover:opacity-90 transition-all disabled:opacity-50">
                            {{ form.processing ? 'Joining...' : 'Create Account' }}
                        </button>
                        <Link href="/login" class="text-center text-sm text-gray-500 hover:text-cyan-400 transition-colors">
                            Already a member? <span class="font-bold text-gray-300">Log in</span>
                        </Link>
                    </div>
                </form>
            </div>
        </div>
      </div>
    </div>
  </AuthLayout>
</template>

<style scoped>
/* Keyframe Animations */
@keyframes slideInLeft {
  from { opacity: 0; transform: translateX(-40px); }
  to { opacity: 1; transform: translateX(0); }
}

@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.98); }
  to { opacity: 1; transform: scale(1); }
}

.animate-slide-in-left {
  animation: slideInLeft 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

.animate-fade-in {
  animation: fadeIn 1s ease-out;
}

/* Custom transitions for inputs */
input, select {
  outline: none !important;
}

/* For browsers that support scrollbar styling */
::-webkit-scrollbar {
  width: 4px;
}
::-webkit-scrollbar-track {
  background: #0e1726;
}
::-webkit-scrollbar-thumb {
  background: #1e293b;
  border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
  background: #3b82f6;
}
</style>