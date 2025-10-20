import { useEffect, useRef, useState } from "react";
import api from "../../../config";
import { Link } from "react-router-dom";
import type { User } from "../../../interfaces/user.interface";
import DataTable from 'datatables.net-dt';
import 'datatables.net-dt/css/dataTables.dataTables.css'; // include styles

function ManageUsers() {
    const [users, setUsers] = useState<User[]>([]);
    const [userId, setUserId] = useState<number | undefined>(0);
    const tableRef = useRef<HTMLTableElement>(null);

    const getUsers = () => {
        api.get("users")
            .then((res) => {
                // console.log(res.data);
                setUsers(res.data);
            })
            .catch((err) => { console.error(err) });
    }

    useEffect(() => {
        document.title = "Manage Users";
        getUsers();
    }, []);

    useEffect(() => {
        if (users.length > 0 && tableRef.current) {
            // Initialize DataTable
            const dt = new DataTable(tableRef.current);
            return () => {
                dt.destroy();
            };
        }
    }, [users]);


    // function handleDelete(id: any){
    function handleDelete(user_id: any) {
        // alert("delete id: "+id);

        // api.delete(`delete-user?id=${id}`)
        api.delete(`delete-user`, {
            params: {
                id: user_id,
                name: "Asia",
            }
        })
            .then((res) => {
                console.log(res.data);
                getUsers();
            })
            .catch((err) => {
                console.error(err);
            })
    }
    return (
        <>
            <div className="container-xxl flex-grow-1 container-p-y">
                <h4 className="fw-bold py-3 mb-4"><span className="text-muted fw-light">Users /</span> Manage</h4>
                <Link to="/create-user" className="btn btn-success">Add New</Link>
                <div className="card mt-3">
                    <div className="table-responsive">
                        <table ref={tableRef} className="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {
                                    users.map((user) => {
                                        return (
                                            <tr key={user.id}>
                                                <td>{user.id}</td>
                                                <td>{user.name}{user.photo && (
                                                    <img
                                                        src={`http://localhost/php-react-api/${user.photo}`}
                                                        alt={user.name}
                                                        style={{ width: "40px", height: "40px", objectFit: "cover", borderRadius: "50%" }}
                                                    />
                                                )}
                                                </td>
                                                <td>{user.email}</td>
                                                <td>{user.role}</td>
                                                <td>
                                                    <div className="d-flex gap-1">
                                                        <Link to={`/post/details/${user.id}`} type="button" className="btn btn-icon btn-outline-info">
                                                            <span className="tf-icons bx bx-search"></span>
                                                        </Link>
                                                        <Link to={`/post/edit/${user.id}`} type="button" className="btn btn-icon btn-outline-primary">
                                                            <span className="tf-icons bx bx-edit"></span>
                                                        </Link>
                                                        <button type="button" className="btn btn-icon btn-outline-danger" onClick={() => setUserId(user?.id)} data-bs-toggle="modal" data-bs-target="#deleteModal">
                                                            <span className="tf-icons bx bx-trash"></span>
                                                        </button>

                                                    </div>
                                                </td>
                                            </tr>
                                        );
                                    })
                                }

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {/* Delete modal */}
            <div className="modal" id="deleteModal" tabIndex={-1}>
                <div className="modal-dialog">
                    <div className="modal-content">
                        <div className="modal-header">
                            <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div className="modal-body text-center">
                            <i className="tf-icons bx bx-trash fs-1 text-danger mb-4"></i>
                            <h5 className="text-center mb-0">Are you sure you want to delete{userId}?</h5>
                        </div>
                        <div className="modal-footer">
                            <button type="button" className="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" className="btn btn-danger" data-bs-dismiss="modal" onClick={() => handleDelete(userId)}>Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </>
    )
}

export default ManageUsers
