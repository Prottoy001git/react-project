import axios from 'axios';
import { useState } from 'react';
import { Link, useParams } from 'react-router-dom'

function DetailsPost() {
    const[id, setId] = useState<number>(0);
    const[userId, setUserId] = useState<number>(0);
    const[title, setTitle] = useState<string>("");
    const[body, setBody] = useState<string>("");

    const params = useParams();
    const queryId = params?.id;

    axios.get(`https://jsonplaceholder.typicode.com/posts/${queryId}`)
    .then ((res)=>{
        // console.log(res.data)
        let data = res.data;
        setId(data.id);
        setUserId(data.userId);
        setTitle(data.title);
        setBody(data.body);
    })
    .catch((err)=>console.error(err));
    return (
        <>
            <div className="container-xxl flex-grow-1 container-p-y">
                <h4 className="fw-bold py-3 mb-4"><Link to="/posts" className="text-muted fw-light">Posts /</Link> Details</h4>
                <div className="card mt-3">
                    <h5 className="card-header">Post Details</h5>
                    <div className="card-body">
                        <p className="card-text"><b>ID:</b> {id}</p>
                        <p className="card-text"><b>User ID:</b> {userId}</p>
                        <p className="card-text"><b>Title:</b> {title}</p>
                        <p className="card-text"><b>Body:</b> {body}</p>
                    </div>
                </div>
            </div>
        </>
    )
}

export default DetailsPost
