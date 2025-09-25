import { useEffect } from 'react'

function Products() {
    // document.title="Products";
    useEffect(()=>{
        document.title="Products";
    },[]);
  return (
    <div>
      <h1>Products List</h1>
    </div>
  )
}

export default Products