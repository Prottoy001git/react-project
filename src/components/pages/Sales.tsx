import { useEffect } from 'react'

function Sales() {
useEffect(()=>{
     document.title="Products";
    },[]);
  return (
    <div>
      <h1>Sales</h1>
    </div>
  )
}

export default Sales
