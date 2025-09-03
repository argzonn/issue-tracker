document.addEventListener('DOMContentLoaded', () => {
  const root = document.querySelector('[data-tags-root]');
  if (!root) return;
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const issueId = root.dataset.issueId;
  const addForm = root.querySelector('form[data-add]');
  const list = root.querySelector('[data-list]');

  const render = (tags) => {
    list.innerHTML = '';
    tags.forEach(t => {
      const li = document.createElement('li');
      li.className = 'list-group-item d-flex justify-content-between align-items-center';
      li.innerHTML = `<span><span class="badge" style="background-color:${t.color ?? '#6c757d'}">&nbsp;</span> ${t.name}</span>
        <button class="btn btn-sm btn-outline-danger" data-remove="${t.id}">Detach</button>`;
      list.appendChild(li);
    });
  };

  addForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const tagId = addForm.querySelector('select[name="tag_id"]').value;
    if (!tagId) return;
    const res = await fetch(`/issues/${issueId}/tags`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify({ tag_id: Number(tagId) })
    });
    const data = await res.json();
    if (data?.ok) render(data.tags);
  });

  list.addEventListener('click', async (e) => {
    const btn = e.target.closest('button[data-remove]');
    if (!btn) return;
    const tagId = btn.getAttribute('data-remove');
    const res = await fetch(`/issues/${issueId}/tags/${tagId}`, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
    });
    const data = await res.json();
    if (data?.ok) render(data.tags);
  });
});
