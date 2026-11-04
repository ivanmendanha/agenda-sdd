import { useEffect, useState } from 'react';
import { getHealth } from '../services/api';

export default function Home() {
    const [status, setStatus] = useState('loading');
    const [error, setError] = useState(null);

    useEffect(() => {
        let cancelled = false;

        getHealth()
            .then((payload) => {
                if (!cancelled) {
                    setStatus(payload.status);
                    setError(null);
                }
            })
            .catch((err) => {
                if (!cancelled) {
                    setError(err.message || 'Failed to reach API');
                    setStatus('error');
                }
            });

        return () => {
            cancelled = true;
        };
    }, []);

    return (
        <main data-react-root="true">
            <h1>Agenda API shell</h1>
            {error ? (
                <p role="alert" data-health-error="true">
                    {error}
                </p>
            ) : (
                <p data-health-status={status}>API status: {status}</p>
            )}
        </main>
    );
}
