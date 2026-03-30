// PAGE NAV
function nav(url){
  window.location.href = url;
}

// MEGA MENU - Robust Implementation
document.addEventListener('DOMContentLoaded', () => {
    const trigger = document.getElementById('menuTrigger');
    const mega = document.getElementById('megaMenu');
    let timeout;

    if (trigger && mega) {
        const show = () => {
            clearTimeout(timeout);
            trigger.classList.add('open');
            mega.classList.add('open');
        };

        const hide = () => {
            timeout = setTimeout(() => {
                trigger.classList.remove('open');
                mega.classList.remove('open');
            }, 150);
        };

        trigger.addEventListener('mouseenter', show);
        trigger.addEventListener('mouseleave', hide);
        mega.addEventListener('mouseenter', show);
        mega.addEventListener('mouseleave', hide);
    }
});

// CART TOAST
function showToast(message = "Added to cart"){
  const t=document.getElementById('toast');
  if (t) {
      const textEl = t.querySelector('.toast-text');
      if (textEl) textEl.textContent = message;
      t.classList.add('show');
      setTimeout(()=>t.classList.remove('show'),2400);
  }
}

// QTY
function qtyChange(d){
    const qtyNum = document.getElementById('qtyNum');
    if (qtyNum) {
        let qty = parseInt(qtyNum.textContent);
        qty = Math.max(1, qty + d);
        qtyNum.textContent = qty;

        const qtyInput = document.getElementById('qtyInput');
        if (qtyInput) qtyInput.value = qty;
    }
}

// PDP THUMBS
function setThumb(el,imgUrl){
  document.querySelectorAll('.pdp-thumb').forEach(t=>t.classList.remove('active'));
  el.classList.add('active');
  const mainImg = document.getElementById('pdpMainImg');
  if (mainImg) mainImg.src=imgUrl;
}

// SIZE CHIPS
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('size-chip')) {
        document.querySelectorAll('.size-chip').forEach(c => c.classList.remove('active'));
        e.target.classList.add('active');
    }
});
