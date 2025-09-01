document.addEventListener('DOMContentLoaded', () => {
  const root = document.querySelector('[data-assignees-root]');
  if (!root) return;
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const issueId = root.dataset.issueId;
  const addForm = root.querySelector('form[data-add]');
  const list = root.querySelector('[data-list]');

  const render = (assignees) => {
    list.innerHTML = '';
    assignees.forEach(a => {
      const li = document.createElement('li');
      li.className = 'd-flex align-items-center justify-content-between';
      li.innerHTML = `<span>${a.name} <small class="text-muted">&lt;${a.email}&gt;</small></span>
        <button class="btn btn-sm btn-outline-danger" data-remove="${a.id}">Remove</button>`;
      list.appendChild(li);
    });
  };

  addForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const userId = addForm.querySelector('select[name="user_id"]').value;
    if (!userId) return;
    const res = await fetch(`/issues/${issueId}/assignees`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify({ user_id: Number(userId) })
    });
    const data = await res.json();
    if (data?.ok) render(data.assignees);
  });

  list.addEventListener('click', async (e) => {
    const btn = e.target.closest('button[data-remove]');
    if (!btn) return;
    const userId = btn.getAttribute('data-remove');
    const res = await fetch(`/issues/${issueId}/assignees/${userId}`, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
    });
    const data = await res.json();
    if (data?.ok) render(data.assignees);
  });
});
