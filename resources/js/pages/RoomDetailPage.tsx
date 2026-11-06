import { useCallback, useEffect, useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import { ApiError, api, type Room } from '../services/api';

type DetailStatus = 'loading' | 'ready' | 'missing' | 'error';

const primaryButtonClasses =
    'rounded bg-[var(--primary)] px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-[var(--primary-dark)]';

const secondaryButtonClasses =
    'rounded border border-[var(--border)] bg-[var(--bg-primary)] px-4 py-2 text-sm font-medium text-[var(--text-secondary)] transition-colors hover:text-[var(--primary)]';

export default function RoomDetailPage() {
    const { id = '' } = useParams();
    const [room, setRoom] = useState<Room | null>(null);
    const [status, setStatus] = useState<DetailStatus>('loading');
    const [message, setMessage] = useState('');

    const load = useCallback(async () => {
        setStatus('loading');
        setMessage('');

        try {
            const body = await api<{ data: Room }>(`/api/rooms/${id}`);

            setRoom(body.data);
            setStatus('ready');
        } catch (error) {
            setStatus(error instanceof ApiError && error.status === 404 ? 'missing' : 'error');
        }
    }, [id]);

    useEffect(() => {
        void load();
    }, [load]);

    const changeSituation = async (action: 'activate' | 'inactivate') => {
        setMessage('');

        try {
            const body = await api<{ data: Room }>(`/api/rooms/${id}/${action}`, { method: 'POST' });

            setRoom(body.data);
        } catch (error) {
            setMessage(error instanceof ApiError && error.message !== '' ? error.message : 'Não foi possível salvar a sala');
        }
    };

    if (status === 'loading') {
        return <p className="text-sm text-[var(--text-secondary)]">Carregando sala...</p>;
    }

    if (status === 'missing') {
        return <p className="text-sm text-[var(--danger)]">Sala não encontrada</p>;
    }

    if (status === 'error' || room === null) {
        return (
            <div className="rounded-lg bg-[var(--bg-primary)] p-6 shadow-[var(--shadow)]">
                <p className="text-sm text-[var(--danger)]">Não foi possível carregar a sala</p>
                <button type="button" onClick={() => void load()} className={`mt-4 ${primaryButtonClasses}`}>
                    Tentar novamente
                </button>
            </div>
        );
    }

    return (
        <section className="rounded-lg bg-[var(--bg-primary)] p-6 shadow-[var(--shadow)]">
            <h1 className="text-xl font-semibold text-[var(--text-primary)]">{room.name}</h1>
            <p className="mt-2 text-sm text-[var(--text-secondary)]">Capacidade: {room.capacity} pessoas</p>

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

            <p
                className={`mt-3 text-xs font-medium ${room.active ? 'text-[var(--success)]' : 'text-[var(--text-tertiary)]'}`}
            >
                {room.active ? 'Ativa' : 'Inativa'}
            </p>

            {message !== '' && (
                <p className="mt-4 text-sm text-[var(--danger)]" role="alert">
                    {message}
                </p>
            )}

            <div className="mt-6 flex flex-wrap gap-3">
                {room.active && (
                    <Link to={`/salas/${room.id}/reservar`} className={primaryButtonClasses}>
                        Reservar
                    </Link>
                )}
                <Link to={`/salas/${room.id}/editar`} className={secondaryButtonClasses}>
                    Editar
                </Link>
                {room.active ? (
                    <button
                        type="button"
                        onClick={() => void changeSituation('inactivate')}
                        className={secondaryButtonClasses}
                    >
                        Inativar
                    </button>
                ) : (
                    <button
                        type="button"
                        onClick={() => void changeSituation('activate')}
                        className={secondaryButtonClasses}
                    >
                        Ativar
                    </button>
                )}
                <Link to="/salas" className={secondaryButtonClasses}>
                    Voltar
                </Link>
            </div>
        </section>
    );
}
