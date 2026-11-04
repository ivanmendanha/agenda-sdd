import { createRoot } from 'react-dom/client';
import Home from './pages/Home';

const root = document.getElementById('app');

if (root) {
    createRoot(root).render(<Home />);
}
