import { useAuth } from './useAuth.js'

export function useApi() {
    const { token, clearAuth } = useAuth()

    const request = async (method, path, body = null) => {
        const res = await fetch(`/api${path}`, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',
                ...(token.value ? { 'Authorization': `Bearer ${token.value}` } : {}),
            },
            ...(body !== null ? { body: JSON.stringify(body) } : {}),
        })

        if (res.status === 401) {
            clearAuth()
            window.location.href = '/admin/login'
            throw new Error('Unauthenticated')
        }

        if (res.status === 204) return null

        const data = await res.json()

        if (!res.ok) {
            const err = new Error(data?.message ?? `HTTP ${res.status}`)
            err.errors = data?.errors ?? {}
            err.status = res.status
            throw err
        }

        return data
    }

    // Multipart (pour les uploads de fichiers)
    const upload = async (path, formData) => {
        const res = await fetch(`/api${path}`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                ...(token.value ? { 'Authorization': `Bearer ${token.value}` } : {}),
                // Pas de Content-Type : le navigateur le positionne avec le boundary multipart
            },
            body: formData,
        })

        if (res.status === 401) { clearAuth(); window.location.href = '/admin/login'; throw new Error('Unauthenticated') }
        if (res.status === 204) return null

        const data = await res.json()
        if (!res.ok) {
            const err = new Error(data?.message ?? `HTTP ${res.status}`)
            err.errors = data?.errors ?? {}
            throw err
        }
        return data
    }

    return {
        get:    (path)        => request('GET',    path),
        post:   (path, body)  => request('POST',   path, body),
        put:    (path, body)  => request('PUT',    path, body),
        patch:  (path, body)  => request('PATCH',  path, body),
        del:    (path)        => request('DELETE', path),
        upload: (path, fd)    => upload(path, fd),
    }
}
