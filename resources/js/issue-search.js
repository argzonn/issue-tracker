document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('issueSearch');
  if (!input) return;
  let t;

  input.addEventListener('input', () => {
    clearTimeout(t);
    t = setTimeout(async () => {
      const params = new URLSearchParams({ q: input.value });
      const res = await fetch(`/issues/search?${params.toString()}`, { headers: { 'Accept': 'application/json' }});
      const data = await res.json();
      if (data?.ok) {
        const tableWrapper = document.querySelector('table.table').parentElement;
        tableWrapper.innerHTML = data.html;
      }
    }, 300);
  });
});
