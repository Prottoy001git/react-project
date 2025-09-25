import { useEffect } from 'react'

function Users() {
useEffect(()=>{
    document.title="Products";
    },[]);
  return (
    <div>
      <h1>Users</h1>
    </div>
  )
}

export default Users
