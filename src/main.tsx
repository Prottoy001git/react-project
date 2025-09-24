import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
import './assets/custom.css'
import { createBrowserRouter, RouterProvider } from 'react-router-dom'
import Layout from './components/layout/Layout.tsx'
import Dashboard from './components/pages/Dashboard.tsx'

const router = createBrowserRouter([
  {path: '/', element: <Layout />,
    children:[
      {index: true, element: <Dashboard />},
      {path:'/dashboard', element: <Dashboard />},
      {path: '/products', element: <h1>Products list</h1>},
    ]
  },
  {path: '/login', element: <h1>login</h1>},
  {path: '*', element: <h1 className="text-danger text-center">404 Page Not Found</h1>},
]);


createRoot(document.getElementById('root')!).render(
  <StrictMode>
    <RouterProvider router={router}/>
  </StrictMode>,
)
