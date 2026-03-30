<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';

defineProps<{
  open: boolean;
  title?: string;
  description?: string;
  loading?: boolean;
}>();

const emit = defineEmits(['close', 'confirm']);
</script>

<template>
  <Dialog :open="open" @update:open="emit('close')">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>{{ title || 'Are you sure?' }}</DialogTitle>
        <DialogDescription>
          {{ description || 'This action cannot be undone.' }}
        </DialogDescription>
      </DialogHeader>
      <DialogFooter>
        <Button variant="outline" @click="emit('close')" :disabled="loading">Cancel</Button>
        <Button variant="destructive" @click="emit('confirm')" :disabled="loading">
          {{ loading ? 'Processing...' : 'Confirm Delete' }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
