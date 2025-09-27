import axios from "axios";
import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
// type Post{
//     userId: number | string;
//     id:number;
//     title:string;
//     body:string;
// }
interface Post {
    userId: number;
    id: number;
    title: string;
    body: string;
}

function ManagePosts() {
    const [posts, setPosts] = useState<Post[]>([]);

    useEffect(() => {
        document.title = "Manage Posts";
        getData();
    }, []);
    useEffect(() => {
        // console.log(posts);
    }, [posts]);

    // fetch API
    // -----------------

    // async function getData() {
    //     try {
    //         const res = await fetch('https://jsonplaceholder.typicode.com/posts')
    //         const data = await res.json();
    //         // console.log(data);
    //         setPosts(data);
    //     } catch (err) {
    //         console.error(err);
    //     }
    // }
    
    // Axios API
    // ----------------------
    function getData(){
        axios.get('https://jsonplaceholder.typicode.com/posts')
        .then ((res)=>{
            // console.log(res.data);
            setPosts(res.data);
        })
        .catch((err)=>{
            console.log(err);
        });

    }


    return (
        <>
            <div className="container-xxl flex-grow-1 container-p-y">
                <h4 className="fw-bold py-3 mb-4"><span className="text-muted fw-light">Posts /</span> Manage</h4>
                <Link to="/posts/create" className="btn btn-success">Add New</Link>
                <div className="card mt-3">
                    <div className="table-responsive">
                        <table className="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User ID</th>
                                    <th>Title</th>
                                    <th>Body</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>1</td>
                                    <td>Test</td>
                                    <td>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorem ipsum alias, numquam, magnam ratione temporibus ullam aspernatur</td>
                                    <td>
                                        <div className="d-flex gap-1">
                                            <button type="button" className="btn btn-icon btn-outline-primary">
                                                <span className="tf-icons bx bx-edit"></span>
                                            </button>
                                            <button type="button" className="btn btn-icon btn-outline-danger">
                                                <span className="tf-icons bx bx-trash"></span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                {
                                    posts.map((item) =>
                                        <tr key={item.id}>
                                            <td>{item.id}</td>
                                            <td>{item.userId}</td>
                                            <td>{item.title}</td>
                                            <td>{item.body}</td>
                                            <td>
                                                <div className="d-flex gap-1">
                                                    <button type="button" className="btn btn-icon btn-outline-primary">
                                                        <span className="tf-icons bx bx-edit"></span>
                                                    </button>
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

export default ManagePosts;