import { type FormEvent, useEffect, useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import { ApiError, api, type Room } from '../services/api';

type RoomForm = {
    name: string;
    capacity: string;
    resources: string;
    active: boolean;
};

const fieldClasses =
    'rounded border border-[var(--border)] bg-[var(--bg-primary)] px-3 py-2 text-sm text-[var(--text-primary)]';

const primaryButtonClasses =
    'rounded bg-[var(--primary)] px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-[var(--primary-dark)]';

const secondaryButtonClasses =
    'rounded border border-[var(--border)] bg-[var(--bg-primary)] px-4 py-2 text-sm font-medium text-[var(--text-secondary)] transition-colors hover:text-[var(--primary)]';

export default function RoomFormPage() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [form, setForm] = useState<RoomForm>({ name: '', capacity: '', resources: '', active: true });
    const [loading, setLoading] = useState(id !== undefined);
    const [message, setMessage] = useState('');

    useEffect(() => {
        if (id === undefined) {
            return;
        }

        setLoading(true);

        api<{ data: Room }>(`/api/rooms/${id}`)
            .then((body) => {
                setForm({
                    name: body.data.name,
                    capacity: String(body.data.capacity),
                    resources: body.data.resources.join(', '),
                    active: body.data.active,
                });
                setLoading(false);
            })
            .catch((error: unknown) => {
                setMessage(
                    error instanceof ApiError && error.status === 404
                        ? 'Sala não encontrada'
                        : 'Não foi possível carregar a sala',
                );
                setLoading(false);
            });
    }, [id]);

    const submit = async (event: FormEvent) => {
        event.preventDefault();
        setMessage('');

        const payload = {
            name: form.name,
            capacity: form.capacity === '' ? null : Number(form.capacity),
            resources: form.resources
                .split(',')
                .map((resource) => resource.trim())
                .filter((resource) => resource !== ''),
            active: form.active,
        };

        try {
            const body = await api<{ data: Room }>(id === undefined ? '/api/rooms' : `/api/rooms/${id}`, {
                method: id === undefined ? 'POST' : 'PUT',
                body: JSON.stringify(payload),
            });

            navigate(`/salas/${body.data.id}`);
        } catch (error) {
            setMessage(
                error instanceof ApiError && (error.status === 422 || error.status === 404)
                    ? error.message
                    : 'Não foi possível salvar a sala',
            );
        }
    };

    if (loading) {
        return <p className="text-sm text-[var(--text-secondary)]">Carregando sala...</p>;
    }

    return (
        <section className="rounded-lg bg-[var(--bg-primary)] p-6 shadow-[var(--shadow)]">
            <h1 className="text-xl font-semibold text-[var(--text-primary)]">
                {id === undefined ? 'Nova sala' : 'Editar sala'}
            </h1>

            <form onSubmit={(event) => void submit(event)} className="mt-6 flex max-w-md flex-col gap-4">
                <label className="flex flex-col gap-1 text-sm text-[var(--text-secondary)]">
                    Nome
                    <input
                        type="text"
                        value={form.name}
                        onChange={(event) => setForm({ ...form, name: event.target.value })}
                        className={fieldClasses}
                    />
                </label>

                <label className="flex flex-col gap-1 text-sm text-[var(--text-secondary)]">
                    Capacidade
                    <input
                        type="number"
                        value={form.capacity}
                        onChange={(event) => setForm({ ...form, capacity: event.target.value })}
                        className={fieldClasses}
                    />
                </label>

                <label className="flex flex-col gap-1 text-sm text-[var(--text-secondary)]">
                    Recursos
                    <input
                        type="text"
                        value={form.resources}
                        onChange={(event) => setForm({ ...form, resources: event.target.value })}
                        className={fieldClasses}
                        placeholder="Projetor, Quadro Branco"
                    />
                </label>

                <label className="flex items-center gap-2 text-sm text-[var(--text-secondary)]">
                    <input
                        type="checkbox"
                        checked={form.active}
                        onChange={(event) => setForm({ ...form, active: event.target.checked })}
                    />
                    Ativa
                </label>

                {message !== '' && (
                    <p className="text-sm text-[var(--danger)]" role="alert">
                        {message}
                    </p>
                )}

                <div className="flex flex-wrap gap-3">
                    <button type="submit" className={primaryButtonClasses}>
                        Salvar
                    </button>
                    <Link to="/salas" className={secondaryButtonClasses}>
                        Cancelar
                    </Link>
                </div>
            </form>
        </section>
    );
}
