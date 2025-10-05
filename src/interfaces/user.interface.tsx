export interface User {
    id?: number,
    name: string,
    email: string,
    role?: string,
    role_id: number,
    address?: string,
    photo?: File,
    password?: string,
}

const userDefault: User = {
    id: 0,
    name: "",
    email: "",
    role: "",
    role_id: 0,
    address: "",
    photo: undefined,
    password: "",
}

export default userDefault;