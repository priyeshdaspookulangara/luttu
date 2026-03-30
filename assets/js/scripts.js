// PAGE NAV
function nav(url){
  window.location.href = url;
}

// MEGA MENU
const trigger=document.getElementById('menuTrigger');
const mega=document.getElementById('megaMenu');
if (trigger && mega) {
    trigger.addEventListener('mouseenter',()=>{trigger.classList.add('open');mega.classList.add('open')});
    const navBar = document.querySelector('nav');
    if (navBar) {
        navBar.addEventListener('mouseleave',()=>{trigger.classList.remove('open');mega.classList.remove('open')});
    }
    mega.addEventListener('mouseenter',()=>{trigger.classList.add('open');mega.classList.add('open')});
    mega.addEventListener('mouseleave',()=>{trigger.classList.remove('open');mega.classList.remove('open')});
}

function closeMega(){
    if (trigger && mega) {
        trigger.classList.remove('open');mega.classList.remove('open')
    }
}

// CART TOAST
function showToast(message = "Added to cart"){
  const t=document.getElementById('toast');
  if (t) {
      t.querySelector('.toast-text').textContent = message;
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
document.querySelectorAll('.size-chip').forEach(chip=>{
  chip.addEventListener('click',()=>{
    document.querySelectorAll('.size-chip').forEach(c=>c.classList.remove('active'));
    chip.classList.add('active');
  });
});
