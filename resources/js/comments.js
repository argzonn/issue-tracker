document.addEventListener('DOMContentLoaded', () => {
  const root = document.querySelector('[data-comments-root]');
  if (!root) return;

  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const issueId = root.dataset.issueId;
  const list = root.querySelector('[data-list]');
  const btnMore = root.querySelector('[data-load-more]');
  const form = root.querySelector('form[data-add]');
  let nextUrl = `/issues/${issueId}/comments`;

  const load = async () => {
    if (!nextUrl) return;
    const res = await fetch(nextUrl, { headers: { 'Accept': 'application/json' }});
    const data = await res.json();
    if (!data?.ok) return;

    list.insertAdjacentHTML('beforeend', data.html);
    nextUrl = data.next;
    btnMore.style.display = nextUrl ? '' : 'none';
  };

  load(); // initial page
  btnMore.addEventListener('click', load);

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(form);
    const payload = Object.fromEntries(fd.entries());

    const res = await fetch(`/issues/${issueId}/comments`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    const data = await res.json();
    if (!data?.ok) return;

    list.insertAdjacentHTML('afterbegin', data.html); // prepend new comment
    form.reset();
  });
});
