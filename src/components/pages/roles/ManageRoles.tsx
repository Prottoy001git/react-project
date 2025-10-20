import { useEffect, useState } from "react";
import { Link } from "react-router-dom"
import api from "../../../config";
import type { Role } from "../../../interfaces/role.interface";

function ManageRoles() {
    const [roles, setRoles] = useState<Role[]>([]);
    const [search, setSearch] = useState<string>("");

    useEffect(()=>{
        getRoles();
    },[]);
    // useEffect(()=>{
    //     console.log(roles);
    // },[roles]);


    function getRoles(){
        api.get("roles")
        .then((res)=>{
            // console.log(res.data);
            setRoles(res.data);
        })
        .catch((err)=>{
            console.error(err);
        })
    }
    const handleFilter =() =>{
        console.log(search);
    }

  return (
    <>
        <div className="container-xxl flex-grow-1 container-p-y">
                <h4 className="fw-bold py-3 mb-4"><span className="text-muted fw-light">Roles /</span> Manage</h4>
                <Link to="/create-role" className="btn btn-success">Add New</Link>
                    <input type="text" className="form-control" placeholder="Search..." value={search} onChange={(e)=>setSearch(e.target.value)} onKeyUp={handleFilter} />
                <div className="card mt-3">
                    <div className="table-responsive">
                        <table className="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                {
                                    roles.map((item)=>
                                        <tr key={item.id}>
                                            <td>{item.id}</td>
                                            <td>{item.name}</td>
                                            <td>
                                                <div>
                                                    <Link to={`/post/details/${item.id}`} type="button" className="btn btn-icon btn-outline-info">
                                                        <span className="tf-icons bx bx-search"></span>
                                                    </Link>
                                                    <Link to={`/post/edit/${item.id}`} type="button" className="btn btn-icon btn-outline-primary">
                                                        <span className="tf-icons bx bx-edit"></span>
                                                    </Link>
                                                    <button type="button" className="btn btn-icon btn-outline-danger">
                                                        <span className="tf-icons bx bx-trash"></span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    )
                                }
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    </>
  )
}

export default ManageRoles
