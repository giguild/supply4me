<template>
  <Teleport to="body">
    <transition name="slide-up">
      <div v-if="showBanner" class="fixed bottom-0 left-0 right-0 z-50 p-4 sm:p-6">
        <div class="max-w-lg mx-auto bg-gradient-to-br from-[#0B1115] via-[#111A20] to-[#0B1115] rounded-2xl border border-[#9F5124]/20 shadow-2xl shadow-black/50 p-6">
          <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-[#9F5124]/20 flex items-center justify-center shrink-0">
              <svg class="w-6 h-6 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <h3 class="text-white font-bold mb-1">Install Supply 4 Me</h3>
              <p class="text-white/60 text-sm mb-4">Add to your home screen for quick access to quality FMCG products.</p>
              <div class="flex gap-3">
                <button @click="installApp" class="flex-1 bg-[#9F5124] text-white py-2.5 rounded-full text-sm font-bold hover:bg-[#8a4620] transition-all duration-300">
                  Install
                </button>
                <button @click="dismiss" class="px-4 py-2.5 rounded-full text-sm font-medium text-white/60 hover:text-white hover:bg-white/10 transition-all duration-300">
                  Not now
                </button>
              </div>
            </div>
            <button @click="dismiss" class="text-white/40 hover:text-white transition-colors shrink-0">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const showBanner = ref(false)
let deferredPrompt = null

onMounted(() => {
  const dismissed = localStorage.getItem('supply4me-pwa-dismissed')
  if (dismissed) return

  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault()
    deferredPrompt = e
    showBanner.value = true
  })

  window.addEventListener('message', (e) => {
    if (e.data?.type === 'INSTALL_PROMPT_READY' && !localStorage.getItem('supply4me-pwa-dismissed')) {
      showBanner.value = true
    }
  })

  window.addEventListener('appinstalled', () => {
    showBanner.value = false
    deferredPrompt = null
  })
})

async function installApp() {
  if (!deferredPrompt) return
  deferredPrompt.prompt()
  const { outcome } = await deferredPrompt.userChoice
  if (outcome === 'accepted') {
    showBanner.value = false
  }
  deferredPrompt = null
}

function dismiss() {
  showBanner.value = false
  localStorage.setItem('supply4me-pwa-dismissed', 'true')
}
</script>

<style scoped>
.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.slide-up-enter-from,
.slide-up-leave-to {
  opacity: 0;
  transform: translateY(100%);
}
</style>
