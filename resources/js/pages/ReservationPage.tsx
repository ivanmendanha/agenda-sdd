import { type FormEvent, useEffect, useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import { ApiError, api, type Reservation, type Room } from '../services/api';

const fieldClasses =
    'rounded border border-[var(--border)] bg-[var(--bg-primary)] px-3 py-2 text-sm text-[var(--text-primary)]';

const primaryButtonClasses =
    'rounded bg-[var(--primary)] px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-[var(--primary-dark)]';

const secondaryButtonClasses =
    'rounded border border-[var(--border)] bg-[var(--bg-primary)] px-4 py-2 text-sm font-medium text-[var(--text-secondary)] transition-colors hover:text-[var(--primary)]';

export default function ReservationPage() {
    const { id = '' } = useParams();
    const [rooms, setRooms] = useState<Room[]>([]);
    const [roomId, setRoomId] = useState(id);
    const [date, setDate] = useState('');
    const [start, setStart] = useState('');
    const [end, setEnd] = useState('');
    const [subject, setSubject] = useState('');
    const [success, setSuccess] = useState('');
    const [message, setMessage] = useState('');

    useEffect(() => {
        api<{ data: Room[] }>('/api/rooms')
            .then((body) => setRooms(body.data))
            .catch(() => setMessage('Não foi possível carregar as salas'));
    }, []);

    useEffect(() => {
        setRoomId(id);
    }, [id]);

    const submit = async (event: FormEvent) => {
        event.preventDefault();
        setSuccess('');
        setMessage('');

        try {
            await api<{ data: Reservation }>('/api/reservations', {
                method: 'POST',
                body: JSON.stringify({ room_id: Number(roomId), date, start, end, subject }),
            });

            setSuccess('Reserva criada com sucesso!');
        } catch (error) {
            setMessage(
                error instanceof ApiError && error.message !== ''
                    ? error.message
                    : 'Não foi possível criar a reserva',
            );
        }
    };

    return (
        <section className="rounded-lg bg-[var(--bg-primary)] p-6 shadow-[var(--shadow)]">
            <h1 className="text-xl font-semibold text-[var(--text-primary)]">Nova reserva</h1>

            <form onSubmit={(event) => void submit(event)} className="mt-6 flex max-w-md flex-col gap-4">
                <label className="flex flex-col gap-1 text-sm text-[var(--text-secondary)]">
                    Sala
                    <select
                        value={roomId}
                        onChange={(event) => setRoomId(event.target.value)}
                        className={fieldClasses}
                    >
                        {rooms.map((room) => (
                            <option key={room.id} value={String(room.id)}>
                                {room.name}
                            </option>
                        ))}
                    </select>
                </label>

                <label className="flex flex-col gap-1 text-sm text-[var(--text-secondary)]">
                    Data
                    <input
                        type="date"
                        value={date}
                        onChange={(event) => setDate(event.target.value)}
                        className={fieldClasses}
                    />
                </label>

                <label className="flex flex-col gap-1 text-sm text-[var(--text-secondary)]">
                    Hora Início
                    <input
                        type="time"
                        value={start}
                        onChange={(event) => setStart(event.target.value)}
                        className={fieldClasses}
                    />
                </label>

                <label className="flex flex-col gap-1 text-sm text-[var(--text-secondary)]">
                    Hora Fim
                    <input
                        type="time"
                        value={end}
                        onChange={(event) => setEnd(event.target.value)}
                        className={fieldClasses}
                    />
                </label>

                <label className="flex flex-col gap-1 text-sm text-[var(--text-secondary)]">
                    Assunto
                    <input
                        type="text"
                        value={subject}
                        onChange={(event) => setSubject(event.target.value)}
                        className={fieldClasses}
                    />
                </label>

                {success !== '' && (
                    <p className="text-sm text-[var(--success)]" role="status">
                        {success}
                    </p>
                )}

                {message !== '' && (
                    <p className="text-sm text-[var(--danger)]" role="alert">
                        {message}
                    </p>
                )}

                <div className="flex flex-wrap gap-3">
                    <button type="submit" className={primaryButtonClasses}>
                        Reservar
                    </button>
                    <Link to={`/salas/${id}`} className={secondaryButtonClasses}>
                        Voltar
                    </Link>
                </div>
            </form>
        </section>
    );
}
