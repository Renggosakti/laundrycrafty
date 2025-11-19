// helper fetch with Authorization header if needed (set token in window.AUTH_TOKEN)
window.AUTH_TOKEN = 'Bearer seblak26';
function fetchAuth(url, opts={}){
  opts.headers = Object.assign(opts.headers||{}, {'Authorization': window.AUTH_TOKEN});
  return fetch(url, opts).then(r=>r.json());
}
function showToast(msg, type='success'){
  const n=document.createElement('div'); n.className='toast position-fixed top-0 end-0 m-3'; n.style.zIndex=9999; n.innerHTML=`<div class="toast-body bg-${type=='success'?'success':'danger'} text-white p-2 rounded">${msg}</div>`; document.body.appendChild(n);
  setTimeout(()=>n.remove(),2500);
}
