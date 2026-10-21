document.documentElement.classList.add('js');
document.addEventListener('DOMContentLoaded',()=>{
 const toggle=document.querySelector('.menu-toggle'),nav=document.querySelector('.site-nav');
 if(toggle&&nav){toggle.addEventListener('click',()=>{const active=nav.classList.toggle('is-open');toggle.setAttribute('aria-expanded',String(active));});document.addEventListener('keydown',e=>{if(e.key==='Escape'&&nav.classList.contains('is-open')){nav.classList.remove('is-open');toggle.setAttribute('aria-expanded','false');toggle.focus();}});nav.querySelectorAll('a').forEach(a=>a.addEventListener('click',()=>{nav.classList.remove('is-open');toggle.setAttribute('aria-expanded','false');}));}
 const targets=document.querySelectorAll('.reveal');if('IntersectionObserver'in window&&!window.matchMedia('(prefers-reduced-motion: reduce)').matches){const observer=new IntersectionObserver(entries=>{entries.forEach(e=>{if(e.isIntersecting){e.target.classList.add('is-visible');observer.unobserve(e.target);}})},{threshold:.08,rootMargin:'0px 0px 40px 0px'});targets.forEach(el=>observer.observe(el));}else targets.forEach(el=>el.classList.add('is-visible'));
});
