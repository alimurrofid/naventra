<template>
  <Card style="width: 25rem; overflow: hidden">
    <template #title>
      <div class="text-center text-primary font-bold text-2xl mb-2">Naventra ERP</div>
      <div class="text-center text-surface-500 text-sm font-normal">Sign in to continue</div>
    </template>
    
    <template #content>
      <form @submit.prevent="handleLogin" class="flex flex-col gap-4 mt-4">
        <Message v-if="errorMsg" severity="error" :closable="false" class="mb-2">{{ errorMsg }}</Message>
        
        <div class="flex flex-col gap-2">
          <label for="email" class="text-sm font-medium">Email</label>
          <InputText id="email" v-model="email" type="email" autocomplete="email" required />
        </div>
        
        <div class="flex flex-col gap-2">
          <label for="password" class="text-sm font-medium">Password</label>
          <Password id="password" v-model="password" :feedback="false" toggleMask autocomplete="current-password" required />
        </div>
        
        <Button type="submit" label="Sign In" icon="pi pi-sign-in" :loading="loading" class="w-full mt-2" />
      </form>
    </template>
  </Card>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/core/store/auth.store';
import Card from 'primevue/card';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Button from 'primevue/button';
import Message from 'primevue/message';

const email = ref(import.meta.env.DEV ? 'admin@admin.com' : '');
const password = ref(import.meta.env.DEV ? 'password' : '');
const loading = ref(false);
const errorMsg = ref('');

const authStore = useAuthStore();
const router = useRouter();

const handleLogin = async () => {
  if (!email.value || !password.value) return;
  
  loading.value = true;
  errorMsg.value = '';
  
  try {
    await authStore.login({
      email: email.value,
      password: password.value
    });
    router.push('/');
  } catch (error: any) {
    errorMsg.value = error.response?.data?.message || 'Login failed. Please check your credentials.';
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.flex { display: flex; }
.flex-col { flex-direction: column; }
.gap-2 { gap: 0.5rem; }
.gap-4 { gap: 1rem; }
.mt-2 { margin-top: 0.5rem; }
.mt-4 { margin-top: 1rem; }
.mb-2 { margin-bottom: 0.5rem; }
.w-full { width: 100%; }
.text-center { text-align: center; }
.text-sm { font-size: 0.875rem; line-height: 1.25rem; }
.text-2xl { font-size: 1.5rem; line-height: 2rem; }
.font-bold { font-weight: 700; }
.font-medium { font-weight: 500; }
.font-normal { font-weight: 400; }
.text-primary { color: var(--p-primary-500); }
.text-surface-500 { color: var(--p-surface-500); }
</style>
