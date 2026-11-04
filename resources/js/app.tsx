import { createRoot } from 'react-dom/client';
import { BrowserRouter, Outlet, Route, Routes } from 'react-router-dom';
import AppLayout from './components/AppLayout';
import NavTabs from './components/NavTabs';
import DashboardPage from './pages/DashboardPage';
import MinhasReservasPage from './pages/MinhasReservasPage';
import NotFoundPage from './pages/NotFoundPage';
import SalasPage from './pages/SalasPage';

function Shell() {
    return (
        <AppLayout>
            <NavTabs />
            <Outlet />
        </AppLayout>
    );
}

const root = document.getElementById('app');

if (root) {
    createRoot(root).render(
        <BrowserRouter>
            <Routes>
                <Route element={<Shell />}>
                    <Route path="/" element={<DashboardPage />} />
                    <Route path="/salas" element={<SalasPage />} />
                    <Route path="/minhas-reservas" element={<MinhasReservasPage />} />
                    <Route path="*" element={<NotFoundPage />} />
                </Route>
            </Routes>
        </BrowserRouter>,
    );
}
