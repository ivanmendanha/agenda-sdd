import { createRoot } from 'react-dom/client';

const root = document.getElementById('app');

if (root) {
    createRoot(root).render(<div data-react-root="true">React shell</div>);
}
