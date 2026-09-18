<template>
  <header class="site-header">
    <a class="brand" href="/" aria-label="Asili Naturals home">
      <span class="brand__mark">A</span>
      <span><strong>ASILI</strong><small>NATURALS</small></span>
    </a>
    <nav class="site-nav" aria-label="Primary navigation">
      <a href="#story">Our story</a>
      <a href="#services">Services</a>
      <a href="#products">Products</a>
      <a href="#principles">Philosophy</a>
    </nav>
    <a class="header-action" href="mailto:hello@asilinaturals.co.ke">Get in touch <span>↗</span></a>
  </header>
  <RouterView />
  <footer class="site-footer">
    <div class="container site-footer__inner">
      <div class="brand brand--footer"><span class="brand__mark">A</span><span><strong>ASILI</strong><small>NATURALS</small></span></div>
      <p>Natural healing, God's provision.</p>
      <span class="site-footer__meta">© {{ new Date().getFullYear() }} Asili Naturals</span>
    </div>
  </footer>
</template>
