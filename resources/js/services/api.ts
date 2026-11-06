export type HealthResponse = {
    status: string;
};

export type Room = {
    id: number;
    name: string;
    capacity: number;
    resources: string[];
    active: boolean;
    availability: 'disponivel' | 'indisponivel' | null;
};

export type Reservation = {
    id: number;
    room_id: number;
    date: string;
    starts_at: string;
    ends_at: string;
    subject: string;
    status: string;
};

/**
 * A failed API response. `message` is the server sentence, or empty when the body carried none,
 * so each page can fall back to its own copy.
 */
export class ApiError extends Error {
    readonly status: number;

    constructor(status: number, message: string) {
        super(message);

        this.name = 'ApiError';
        this.status = status;
    }
}

export async function api<T>(path: string, init?: RequestInit): Promise<T> {
    const response = await fetch(path, {
        ...init,
        headers: {
            Accept: 'application/json',
            ...(init?.body === undefined ? {} : { 'Content-Type': 'application/json' }),
            ...init?.headers,
        },
    });

    const body = await readJson(response);

    if (!response.ok) {
        throw new ApiError(response.status, serverMessage(body));
    }

    return body as T;
}

export async function getHealth(): Promise<HealthResponse> {
    return api<HealthResponse>('/api/health');
}

async function readJson(response: Response): Promise<unknown> {
    const text = await response.text();

    if (text === '') {
        return null;
    }

    try {
        return JSON.parse(text) as unknown;
    } catch {
        return null;
    }
}

function serverMessage(body: unknown): string {
    if (typeof body !== 'object' || body === null || !('message' in body)) {
        return '';
    }

    const message = (body as { message: unknown }).message;

    return typeof message === 'string' ? message : '';
}
