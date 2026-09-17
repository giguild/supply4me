<template>
  <div class="min-h-screen">
    <MarketingHeader :customer="$page.props.auth?.customer" />
    
    <main>
      <HeroSection />
      <CategorySection :categories="categories" />
      <HowItWorksSection />
      <BusinessGrowthSection />
      <MetricsStrip :stats="stats" />
      <AppCTASection />
      <TestimonialsSection :testimonials="testimonials" />
    </main>

    <MarketingFooter :customer="$page.props.auth?.customer" :cartCount="cartCount" />
  </div>
</template>

<script setup>
import MarketingHeader from '@/Components/Landing/MarketingHeader.vue'
import HeroSection from '@/Components/Landing/HeroSection.vue'
import CategorySection from '@/Components/Landing/CategorySection.vue'
import HowItWorksSection from '@/Components/Landing/HowItWorksSection.vue'
import BusinessGrowthSection from '@/Components/Landing/BusinessGrowthSection.vue'
import MetricsStrip from '@/Components/Landing/MetricsStrip.vue'
import AppCTASection from '@/Components/Landing/AppCTASection.vue'
import TestimonialsSection from '@/Components/Landing/TestimonialsSection.vue'
import MarketingFooter from '@/Components/Landing/MarketingFooter.vue'

const props = defineProps({
  categories: { type: Array, default: () => [] },
  brands: { type: Array, default: () => [] },
  featured: { type: Array, default: () => [] },
  cartCount: { type: Number, default: 0 },
  company: { type: Object, default: () => ({}) },
  stats: { type: Object, default: () => ({}) },
  testimonials: { type: Array, default: () => [] },
})
</script>
