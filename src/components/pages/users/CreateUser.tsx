import { Link } from "react-router-dom"
import api from "../../../config"
import { useEffect, useState } from "react"
import type { Role } from "../../../interfaces/role.interface";

function CreateUser() {
    const [roles, setRole] = useState<Role[]>([]);
    useEffect(()=>{
        document.title = "Create user";
        getRoles();
    },[]);
    const getRoles = () =>{
        api.get ("roles")
        .then((res)=> {
            // console.log(res.data);
            setRole(res.data);
        })
        .catch((err) =>{
            console.error(err);
        })
    }
  return (
    <>
        <div className="container-xxl flex-grow-1 container-p-y">
                <h4 className="fw-bold py-3 mb-4"><Link to="/users" className="text-muted fw-light">Users /</Link> Create</h4>
                <div className="card mt-3">
                    <h5 className="card-header">Create User</h5>
                    <div className="card-body">
                        <form >
                            <div className="mb-3">
                                <label className="form-label">Name</label>
                                <input type="text" name="name" className="form-control" />
                            </div>
                            <div className="mb-3">
                                <label className="form-label">Email</label>
                                <input type="text" name="email" className="form-control" />
                            </div>
                            <div className="mb-3">
                                <label className="form-label">Role</label>
                                <select name="role_id" className="form-select">
                                    <option selected disabled>Select Role</option>
                                    {
                                        roles.map((role)=>
                                            <option value={role.id} key={role.id}>{role.name}</option>
                                        )
                                    }
                                </select>
                            </div>
                            <div className="mb-3">
                                <label className="form-label">Address</label>
                                <textarea name="body" className="form-control" rows={4} ></textarea>
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
