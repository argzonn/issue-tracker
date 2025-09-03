document.addEventListener('DOMContentLoaded', () => {
  const form  = document.getElementById('comment-form');
  if (!form) return;

  const list  = document.getElementById('comments-list');
  const body  = document.getElementById('comment-body');
  const count = document.getElementById('comment-count');
  const url   = form.dataset.url;
  const csrf  = document.querySelector('meta[name="csrf-token"]')?.content || '';

  let busy = false;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (busy) return;

    const text = (body?.value || '').trim();
    if (!text) return;

    busy = true;
    try {
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrf,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ body: text })
      });

      if (!res.ok) {
        console.error('Comment post failed', res.status, await res.text());
        alert('Failed to post comment.');
        return;
      }

      const data = await res.json();
      if (data?.ok && data?.html) {
        const t = document.createElement('template');
        t.innerHTML = data.html.trim();
        const li = t.content.firstElementChild;
        if (li) list.prepend(li);
        if (count && typeof data.count === 'number') {
          count.textContent = `${data.count} comments`;
        }
        body.value = '';
      } else {
        console.error('Unexpected response', data);
      }
    } catch (err) {
      console.error('Comment post error', err);
    } finally {
      busy = false;
    }
  });
});
