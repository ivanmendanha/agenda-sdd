import { NavLink } from 'react-router-dom';

const tabs = [
    { to: '/', label: 'Dashboard', end: true },
    { to: '/salas', label: 'Salas', end: false },
    { to: '/minhas-reservas', label: 'Minhas Reservas', end: false },
] as const;

export default function NavTabs() {
    return (
        <nav
            className="mb-6 flex flex-wrap gap-2 border-b-2 border-[var(--border)]"
            aria-label="Principal"
        >
            {tabs.map((tab) => (
                <NavLink
                    key={tab.to}
                    to={tab.to}
                    end={tab.end}
                    className={({ isActive }) =>
                        [
                            '-mb-0.5 border-b-2 px-5 py-3 text-sm font-medium transition-colors',
                            isActive
                                ? 'border-[var(--primary)] text-[var(--primary)]'
                                : 'border-transparent text-[var(--text-secondary)] hover:text-[var(--primary)]',
                        ].join(' ')
                    }
                >
                    {tab.label}
                </NavLink>
            ))}
        </nav>
    );
}
