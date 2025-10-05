import { Link } from "react-router-dom"
import api from "../../../config"
import { useEffect, useState } from "react"
import type { Role } from "../../../interfaces/role.interface";
import type { User } from "../../../interfaces/user.interface";
import userDefault from "../../../interfaces/user.interface";

function CreateUser() {
    const [roles, setRole] = useState<Role[]>([]);
    const [user, setUser] = useState<User>(userDefault);
    useEffect(() => {
        document.title = "Create user";
        getRoles();
    }, []);
    const getRoles = () => {
        api.get("roles")
            .then((res) => {
                // console.log(res.data);
                setRole(res.data);
            })
            .catch((err) => {
                console.error(err);
            })
    }
    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        // console.log(user);
        const formdata = new FormData();    //built in class (FormData), sends data through form
        formdata.append("name", user.name);
        formdata.append("email", user.email);
        formdata.append("address", user.address ?? "");
        formdata.append("role_id", user.role_id.toString());
        if (user.photo) formdata.append("photo", user.photo);

        console.log(formdata);
        // for (let pair of formdata.entries()) {
        //     console.log(pair[0] + ': ' + pair[1]);
        // }

        api.post("create-user", formdata, {
            headers: {
                "Content-Type": "multipart/form-data"
            }
        })
            .then((res) => {
                console.log(res.data);
            })
            .catch((err) => {
                console.error(err)
            })
    }
    return (
        <>
            <div className="container-xxl flex-grow-1 container-p-y">
                <h4 className="fw-bold py-3 mb-4"><Link to="/users" className="text-muted fw-light">Users /</Link> Create</h4>
                <div className="card mt-3">
                    <h5 className="card-header">Create User</h5>
                    <div className="card-body">
                        <form onSubmit={handleSubmit}>
                            <div className="mb-3">
                                <label className="form-label">Name</label>
                                <input type="text" name="name" className="form-control" value={user.name} onChange={(e) => setUser({ ...user, name: e.target.value })} />
                            </div>
                            <div className="mb-3">
                                <label className="form-label">Email</label>
                                <input type="text" name="email" className="form-control" value={user.email} onChange={(e) => setUser({ ...user, email: e.target.value })} />
                            </div>
                            <div className="mb-3">
                                <label className="form-label">Role</label>
                                <select name="role_id" className="form-select" onChange={(e) => setUser({ ...user, role_id: parseInt(e.target.value) })}>
                                    <option selected disabled>Select Role</option>
                                    {
                                        roles.map((role) =>
                                            <option value={role.id} key={role.id}>{role.name}</option>
                                        )
                                    }
                                </select>
                            </div>
                            <div className="mb-3">
                                <label className="form-label">Address</label>
                                <textarea name="body" className="form-control" rows={4} value={user.address} onChange={(e) => setUser({ ...user, address: e.target.value })}></textarea>
                            </div>
                            <div className="mb-3">
                                <label className="form-label">Photo</label>
                                <input type="file" name="photo" className="form-control" onChange={(e) => {
                                    if (e.target.files !== null) setUser({ ...user, photo: e.target.files[0] })
                                }} />
                            </div>
                            <button type="submit" className="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </>
    )
}

export default CreateUser
