import { useState } from "react"
import { Link } from "react-router-dom"
import api from "../../../config";
import type { Role } from "../../../interfaces/role.interface";

function CreateRole() {
    const[role, setRole] = useState<Role>({
        id: 0,
        name:""
    });

    const handleSubmit = (e:React.FormEvent) => {
        e.preventDefault();
        api.post("create-role", role)
            .then((res) => {
                console.log(res);
                alert("Data Saved Successfully");
            })
            .catch((err) => {
                console.error(err);
            });
    }
    return (
        <>
            <div className="container-xxl flex-grow-1 container-p-y">
                <h4 className="fw-bold py-3 mb-4"><Link to="/roles" className="text-muted fw-light">Roles /</Link> Create</h4>
                <div className="card mt-3">
                    <h5 className="card-header">Create Role</h5>
                    <div className="card-body">
                        <form onSubmit={handleSubmit}>
                            <div className="mb-3">
                                <label className="form-label">Title</label>
                                <input type="text" name="title" className="form-control" value={role.name} onChange={(e)=>setRole({...role, name:e.target.value})} />
                            </div>
                            <button type="submit" className="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>

        </>
    )
}

export default CreateRole
