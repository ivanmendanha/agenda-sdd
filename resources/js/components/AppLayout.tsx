import type { ReactNode } from 'react';

type AppLayoutProps = {
    children: ReactNode;
};

export default function AppLayout({ children }: AppLayoutProps) {
    return (
        <div data-react-root="true">
            <header className="mb-6 bg-[var(--bg-primary)] py-4 shadow-[var(--shadow)]">
                <div className="mx-auto flex max-w-[1200px] items-center justify-between px-5">
                    <div className="text-2xl font-semibold text-[var(--primary)]">
                        📅 Reserva de Salas
                    </div>
                </div>
            </header>
            <div className="mx-auto max-w-[1200px] px-5">{children}</div>
        </div>
    );
}
