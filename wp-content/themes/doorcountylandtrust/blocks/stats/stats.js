/* DCLT Stats block: accessible counting animation */
(function(){
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function format(n){
    return n.toLocaleString(undefined);
  }
  function animateCount(el, to, duration){
    if (prefersReduced || !('requestAnimationFrame' in window)){
      el.textContent = format(to);
      return;
    }
    const start = 0;
    const d = Math.max(400, parseInt(el.dataset.duration || duration || 1600,10));
    const t0 = performance.now();
    const ease = (t)=>1-Math.pow(1-t,3); // easeOutCubic

    function frame(now){
      const p = Math.min(1, (now - t0) / d);
      const val = Math.round(start + (to - start) * ease(p));
      el.textContent = format(val);
      if (p < 1) requestAnimationFrame(frame);
    }
    requestAnimationFrame(frame);
  }

  function init(root){
    const stats = root.querySelectorAll('.js-count');
    if (!stats.length) return;

    const io = ('IntersectionObserver' in window) ?
      new IntersectionObserver((entries, obs)=>{
        entries.forEach(entry=>{
          if (!entry.isIntersecting) return;
          const el = entry.target;
          obs.unobserve(el);
          const target = parseInt(el.getAttribute('data-target') || '0', 10) || 0;
          animateCount(el, target, parseInt(el.getAttribute('data-duration')||'1600',10));
        });
      }, { threshold: 0.4 }) : null;

    stats.forEach(el=>{
      if (io) io.observe(el);
      else {
        const target = parseInt(el.getAttribute('data-target') || '0', 10) || 0;
        animateCount(el, target, 1600);
      }
    });
  }

  document.addEventListener('DOMContentLoaded', ()=>{
    document.querySelectorAll('[data-dclt-stats]').forEach(init);
  });
})();