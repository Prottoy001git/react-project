import { Outlet } from "react-router-dom"
import Dashboard from "../pages/Dashboard"
import Footer from "./Footer"
import Navbar from "./Navbar"
import Sidebar from "./Sidebar"

function Layout() {
    
    return (
        <>
            {/* <!-- Layout wrapper --> */}
            <div className="layout-wrapper layout-content-navbar">
                <div className="layout-container">
                    {/* <!-- Menu --> */}
                    <Sidebar />
                    {/* <!-- / Menu --> */}

                    {/* <!-- Layout container --> */}
                    <div className="layout-page">
                        {/* <!-- Navbar --> */}
                        <Navbar />
                        {/* <!-- / Navbar --> */}

                        {/* <!-- Content wrapper --> */}
                        <div className="content-wrapper">
                            {/* <!-- Content --> */}
                            <Outlet />
                            {/* <!-- / Content --> */}

                            {/* <!-- Footer --> */}
                            <Footer />
                            {/* <!-- / Footer --> */}

                            <div className="content-backdrop fade"></div>
                        </div>
                        {/* <!-- Content wrapper --> */}
                    </div>
                    {/* <!-- / Layout page --> */}
                </div>

                {/* <!-- Overlay --> */}
                <div className="layout-overlay layout-menu-toggle"></div>
            </div>
            {/* <!-- / Layout wrapper --> */}
        </>
    )
}

export default Layout