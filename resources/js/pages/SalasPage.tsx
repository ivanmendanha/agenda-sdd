import { useCallback, useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { ApiError, api, type Room } from '../services/api';

type Filters = {
    minCapacity: string;
    resource: string;
};

type Interval = {
    date: string;
    start: string;
    end: string;
};

type ListStatus = 'loading' | 'ready' | 'error';

const blankFilters: Filters = { minCapacity: '', resource: '' };

const fieldClasses =
    'rounded border border-[var(--border)] bg-[var(--bg-primary)] px-3 py-2 text-sm text-[var(--text-primary)]';

const primaryButtonClasses =
    'rounded bg-[var(--primary)] px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-[var(--primary-dark)]';

const secondaryButtonClasses =
    'rounded border border-[var(--border)] bg-[var(--bg-primary)] px-4 py-2 text-sm font-medium text-[var(--text-secondary)] transition-colors hover:text-[var(--primary)]';

export default function SalasPage() {
    const [filters, setFilters] = useState<Filters>(blankFilters);
    const [availability, setAvailability] = useState<Interval>({ date: '', start: '', end: '' });
    const [rooms, setRooms] = useState<Room[]>([]);
    const [appliedFilters, setAppliedFilters] = useState<Filters>(blankFilters);
    const [status, setStatus] = useState<ListStatus>('loading');
    const [message, setMessage] = useState('');

    const load = useCallback(async (applied: Filters, queried: Interval | null) => {
        setStatus('loading');
        setMessage('');

        try {
            const body = await api<{ data: Room[] }>(`/api/rooms${queryOf(applied, queried)}`);

            setRooms(body.data);
            setAppliedFilters(applied);
            setStatus('ready');
        } catch (error) {
            if (error instanceof ApiError && error.status === 422) {
                setRooms((previous) => previous.map((room) => ({ ...room, availability: null })));
                setMessage(error.message);
                setStatus('ready');

                return;
            }

            setStatus('error');
        }
    }, []);

    useEffect(() => {
        void load(blankFilters, null);
    }, [load]);

    const clear = () => {
        setFilters(blankFilters);
        void load(blankFilters, null);
    };

    const hasAppliedFilters = appliedFilters.minCapacity.trim() !== '' || appliedFilters.resource.trim() !== '';

    return (
        <section>
            <div className="mb-6 flex flex-wrap items-center justify-between gap-4">
                <h1 className="text-xl font-semibold text-[var(--text-primary)]">Salas</h1>
                <Link to="/salas/nova" className={primaryButtonClasses}>
                    Nova sala
                </Link>
            </div>

            <div className="mb-6 rounded-lg bg-[var(--bg-primary)] p-6 shadow-[var(--shadow)]">
                <div className="flex flex-wrap items-end gap-4">
                    <label className="flex flex-col gap-1 text-sm text-[var(--text-secondary)]">
                        Capacidade mínima
                        <input
                            type="number"
                            value={filters.minCapacity}
                            onChange={(event) => setFilters({ ...filters, minCapacity: event.target.value })}
                            className={fieldClasses}
                        />
                    </label>
                    <label className="flex flex-col gap-1 text-sm text-[var(--text-secondary)]">
                        Recurso
                        <input
                            type="text"
                            value={filters.resource}
                            onChange={(event) => setFilters({ ...filters, resource: event.target.value })}
                            className={fieldClasses}
                        />
                    </label>
                    <button type="button" onClick={() => void load(filters, null)} className={primaryButtonClasses}>
                        Filtrar
                    </button>
                    <button type="button" onClick={clear} className={secondaryButtonClasses}>
                        Limpar
                    </button>
                </div>

                <div className="mt-4 flex flex-wrap items-end gap-4 border-t border-[var(--border)] pt-4">
                    <label className="flex flex-col gap-1 text-sm text-[var(--text-secondary)]">
                        Data
                        <input
                            type="date"
                            value={availability.date}
                            onChange={(event) => setAvailability({ ...availability, date: event.target.value })}
                            className={fieldClasses}
                        />
                    </label>
                    <label className="flex flex-col gap-1 text-sm text-[var(--text-secondary)]">
                        Hora Início
                        <input
                            type="time"
                            value={availability.start}
                            onChange={(event) => setAvailability({ ...availability, start: event.target.value })}
                            className={fieldClasses}
                        />
                    </label>
                    <label className="flex flex-col gap-1 text-sm text-[var(--text-secondary)]">
                        Hora Fim
                        <input
                            type="time"
                            value={availability.end}
                            onChange={(event) => setAvailability({ ...availability, end: event.target.value })}
                            className={fieldClasses}
                        />
                    </label>
                    <button type="button" onClick={() => void load(filters, availability)} className={primaryButtonClasses}>
                        Consultar disponibilidade
                    </button>
                </div>

                {message !== '' && (
                    <p className="mt-4 text-sm text-[var(--danger)]" role="alert">
                        {message}
                    </p>
                )}
            </div>

            {status === 'loading' && <p className="text-sm text-[var(--text-secondary)]">Carregando salas...</p>}

            {status === 'error' && (
                <div className="rounded-lg bg-[var(--bg-primary)] p-6 shadow-[var(--shadow)]">
                    <p className="text-sm text-[var(--danger)]">Não foi possível carregar as salas</p>
                    <button
                        type="button"
                        onClick={() => void load(appliedFilters, null)}
                        className={`mt-4 ${primaryButtonClasses}`}
                    >
                        Tentar novamente
                    </button>
                </div>
            )}

            {status === 'ready' && rooms.length === 0 && (
                <p className="text-sm text-[var(--text-secondary)]">
                    {hasAppliedFilters ? 'Nenhuma sala encontrada' : 'Nenhuma sala cadastrada'}
                </p>
            )}

            {status === 'ready' && rooms.length > 0 && (
                <div className="grid grid-cols-1 gap-5 min-[769px]:grid-cols-3">
                    {rooms.map((room) => (
                        <RoomCard key={room.id} room={room} />
                    ))}
                </div>
            )}
        </section>
    );
}

function RoomCard({ room }: { room: Room }) {
    return (
        <Link
            to={`/salas/${room.id}`}
            className="block rounded-lg bg-[var(--bg-primary)] p-5 shadow-[var(--shadow)] transition-all hover:-translate-y-0.5 hover:shadow-[var(--shadow-hover)]"
        >
            <div className="text-lg font-semibold text-[var(--text-primary)]">{room.name}</div>
            <div className="mt-2 text-sm text-[var(--text-secondary)]">Capacidade: {room.capacity} pessoas</div>
            <div className="mt-3 flex flex-wrap gap-2">
                {room.resources.map((resource) => (
                    <span
                        key={resource}
                        className="rounded bg-[var(--bg-secondary)] px-2 py-1 text-xs text-[var(--text-secondary)]"
                    >
                        {resource}
                    </span>
                ))}
            </div>
            <div className="mt-3 flex flex-wrap gap-2 text-xs font-medium">
                <span className={room.active ? 'text-[var(--success)]' : 'text-[var(--text-tertiary)]'}>
                    {room.active ? 'Ativa' : 'Inativa'}
                </span>
                {room.active && room.availability !== null && (
                    <span
                        className={
                            room.availability === 'disponivel' ? 'text-[var(--success)]' : 'text-[var(--danger)]'
                        }
                    >
                        {room.availability === 'disponivel' ? 'Disponível' : 'Indisponível'}
                    </span>
                )}
            </div>
        </Link>
    );
}

function queryOf(filters: Filters, interval: Interval | null): string {
    const params = new URLSearchParams();

    if (filters.minCapacity.trim() !== '') {
        params.set('min_capacity', filters.minCapacity.trim());
    }

    if (filters.resource.trim() !== '') {
        params.set('resource', filters.resource.trim());
    }

    if (interval !== null) {
        params.set('date', interval.date);
        params.set('start', interval.start);
        params.set('end', interval.end);
    }

    const query = params.toString();

    return query === '' ? '' : `?${query}`;
}
