(function(){
  const toggle = document.getElementById('darkToggle');
  const pref = localStorage.getItem('dark') === '1';
  if (pref) document.documentElement.classList.add('dark');
  if (toggle) {
    toggle.checked = pref;
    toggle.addEventListener('change', () => {
      document.documentElement.classList.toggle('dark', toggle.checked);
      localStorage.setItem('dark', toggle.checked ? '1' : '0');
    });
  }
  document.querySelectorAll('form').forEach(f => {
    f.addEventListener('submit', ev => {
      if (!f.checkValidity()) {
        ev.preventDefault();
        const firstInvalid = f.querySelector(':invalid');
        if (firstInvalid) firstInvalid.focus();
      }
    });
  });
})();