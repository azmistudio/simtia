
<template>
  <div ref="codefund" id="codefund"></div>
</template>

<script>
export default {
  name: 'CodeFund',
  props: {
    propertyId: { type: String, required: true },
  },
  mounted() {
    this.load();
  },
  watch: {
    $route(to, from) {
      if (to.path !== from.path) {
        this.$refs.codefund.innerHTML = '';
        this.load();
      }
    },
  },
  methods: {
    load() {
      const script = document.createElement('script');
      script.setAttribute('type', 'text/javascript');
      script.setAttribute('src', `https://codefund.app/properties/${this.propertyId}/funder.js?ts=${Date.now()}`);
      this.$refs.codefund.appendChild(script);
    },
  },
};
</script>