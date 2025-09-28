import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
import './assets/custom.css'
import { createBrowserRouter, RouterProvider } from 'react-router-dom'
import Layout from './components/layout/Layout.tsx'
import Dashboard from './components/pages/Dashboard.tsx'
import Products from './components/pages/Products.tsx'
import Roles from './components/pages/Roles.tsx'
import Sales from './components/pages/Sales.tsx'
import Users from './components/pages/Users.tsx'
import ManagePosts from './components/pages/posts/ManagePosts.tsx'
import CreatePost from './components/pages/posts/CreatePost.tsx'
import DetailsPost from './components/pages/posts/DetailsPost.tsx'
import EditPost from './components/pages/posts/EditPost.tsx'

const router = createBrowserRouter([
  {path: '/', element: <Layout />,
    children:[
      {index: true, element: <Dashboard />},
      {path:'/dashboard', element: <Dashboard />},
      {path: '/products', element: <Products />},
      {path: '/sales', element: <Sales />},
      {path: '/users', element: <Users />},
      {path: '/roles', element: <Roles />},
      {path: '/posts', element: <ManagePosts />},
      {path: '/post/create', element: <CreatePost />},
      {path: '/post/details/:id', element: <DetailsPost />},
      {path: '/post/edit/:id', element: <EditPost />},
    ]
  },
  {path: '/pos', element: <h1>POS</h1>},
  {path: '/login', element: <h1>login</h1>},
  {path: '*', element: <h1 className="text-danger text-center">404 Page Not Found</h1>},
]);


createRoot(document.getElementById('root')!).render(
  <StrictMode>
    <RouterProvider router={router}/>
  </StrictMode>,
)
