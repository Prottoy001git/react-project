import { useState } from "react"
import { NavLink } from "react-router-dom"

function Sidebar() {
    const [isOpen, setIsOpen] = useState(false);

    const toggle = () => setIsOpen((prev) => !prev);
    return (
        <>
            <input type="checkbox" id="sidebarMenu" className="d-none" />
            <label htmlFor="sidebarMenu" className="bg-layer"></label>
            <aside id="layout-menu" className="layout-menu menu-vertical menu bg-menu-theme">
                <div className="app-brand demo">
                    <a href="index.html" className="app-brand-link">
                        <span className="app-brand-logo demo">

                        </span>
                        <span className="app-brand-text demo menu-text fw-bolder ms-2">Sneat</span>
                    </a>

                    <label htmlFor="sidebarMenu" className="menu-link text-large ms-auto d-block d-xl-none text-danger">
                        <i className="bx bx-x bx-md align-middle"></i>
                    </label>
                </div>

                <div className="menu-inner-shadow"></div>

                <ul className="menu-inner py-1">
                    {/* <!-- Dashboard --> */}
                    <NavLink to="/dashboard" className={({ isActive }) => isActive ? "menu-item active" : "menu-item"}>
                        <li className="menu-link">
                            <i className="menu-icon tf-icons bx bx-home-circle"></i>
                            <div data-i18n="Analytics">Dashboard</div>
                        </li>
                    </NavLink>
                    {/* <!-- Custom Menu --> */}
                    <NavLink to="/pos" className="menu-item" target="_blank">
                        <li className="menu-link">
                            <i className="menu-icon tf-icons bx bx-layout"></i>
                            <div data-i18n="Analytics">POS</div>
                        </li>
                    </NavLink>
                    <NavLink to="/products" className="menu-item">
                        <li className="menu-link">
                            <i className="menu-icon tf-icons bx bx-package"></i>
                            <div data-i18n="Analytics">Products</div>
                        </li>
                    </NavLink>
                    <NavLink to="/users" className="menu-item">
                        <li className="menu-link">
                            <i className="menu-icon tf-icons bx bx-user"></i>
                            <div data-i18n="Analytics">Users</div>
                        </li>
                    </NavLink>
                    <NavLink to="/roles" className="menu-item">
                        <li className="menu-link">
                            <i className="menu-icon tf-icons bx bx-briefcase-alt-2"></i>
                            <div data-i18n="Analytics">Roles</div>
                        </li>
                    </NavLink>
                    <NavLink to="/posts" className="menu-item">
                        <li className="menu-link">
                            <i className="menu-icon tf-icons bx bx-message-minus"></i>
                            <div data-i18n="Analytics">Posts</div>
                        </li>
                    </NavLink>

                    {/* <!-- Layouts --> */}
                    <li className="menu-item">
                        <span onClick={toggle} className="menu-link menu-toggle cursor-pointer">
                            <i className="menu-icon tf-icons bx bx-layout"></i>
                            <div data-i18n="Layouts">Layouts</div>
                        </span>

                        <ul className={`menu-sub ${isOpen ? 'open' : 'closed'}`}>
                            <li className="menu-item">
                                <a href="layouts-without-menu.html" className="menu-link">
                                    <div data-i18n="Without menu">Without menu</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="layouts-without-navbar.html" className="menu-link">
                                    <div data-i18n="Without navbar">Without navbar</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="layouts-container.html" className="menu-link">
                                    <div data-i18n="Container">Container</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="layouts-fluid.html" className="menu-link">
                                    <div data-i18n="Fluid">Fluid</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="layouts-blank.html" className="menu-link">
                                    <div data-i18n="Blank">Blank</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li className="menu-header small text-uppercase">
                        <span className="menu-header-text">Pages</span>
                    </li>
                    <li className="menu-item">
                        <a href="javascript:void(0);" className="menu-link menu-toggle">
                            <i className="menu-icon tf-icons bx bx-dock-top"></i>
                            <div data-i18n="Account Settings">Account Settings</div>
                        </a>
                        <ul className="menu-sub">
                            <li className="menu-item">
                                <a href="pages-account-settings-account.html" className="menu-link">
                                    <div data-i18n="Account">Account</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="pages-account-settings-notifications.html" className="menu-link">
                                    <div data-i18n="Notifications">Notifications</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="pages-account-settings-connections.html" className="menu-link">
                                    <div data-i18n="Connections">Connections</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li className="menu-item">
                        <a href="javascript:void(0);" className="menu-link menu-toggle">
                            <i className="menu-icon tf-icons bx bx-lock-open-alt"></i>
                            <div data-i18n="Authentications">Authentications</div>
                        </a>
                        <ul className="menu-sub">
                            <li className="menu-item">
                                <a href="auth-login-basic.html" className="menu-link" target="_blank">
                                    <div data-i18n="Basic">Login</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="auth-register-basic.html" className="menu-link" target="_blank">
                                    <div data-i18n="Basic">Register</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="auth-forgot-password-basic.html" className="menu-link" target="_blank">
                                    <div data-i18n="Basic">Forgot Password</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li className="menu-item">
                        <a href="javascript:void(0);" className="menu-link menu-toggle">
                            <i className="menu-icon tf-icons bx bx-cube-alt"></i>
                            <div data-i18n="Misc">Misc</div>
                        </a>
                        <ul className="menu-sub">
                            <li className="menu-item">
                                <a href="pages-misc-error.html" className="menu-link">
                                    <div data-i18n="Error">Error</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="pages-misc-under-maintenance.html" className="menu-link">
                                    <div data-i18n="Under Maintenance">Under Maintenance</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    {/* <!-- Components --> */}
                    <li className="menu-header small text-uppercase"><span className="menu-header-text">Components</span></li>
                    {/* <!-- Cards --> */}
                    <li className="menu-item">
                        <a href="cards-basic.html" className="menu-link">
                            <i className="menu-icon tf-icons bx bx-collection"></i>
                            <div data-i18n="Basic">Cards</div>
                        </a>
                    </li>
                    {/* <!-- User interface --> */}
                    <li className="menu-item">
                        <a href="javascript:void(0)" className="menu-link menu-toggle">
                            <i className="menu-icon tf-icons bx bx-box"></i>
                            <div data-i18n="User interface">User interface</div>
                        </a>
                        <ul className="menu-sub">
                            <li className="menu-item">
                                <a href="ui-accordion.html" className="menu-link">
                                    <div data-i18n="Accordion">Accordion</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="ui-alerts.html" className="menu-link">
                                    <div data-i18n="Alerts">Alerts</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="ui-badges.html" className="menu-link">
                                    <div data-i18n="Badges">Badges</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="ui-buttons.html" className="menu-link">
                                    <div data-i18n="Buttons">Buttons</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="ui-carousel.html" className="menu-link">
                                    <div data-i18n="Carousel">Carousel</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="ui-collapse.html" className="menu-link">
                                    <div data-i18n="Collapse">Collapse</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="ui-dropdowns.html" className="menu-link">
                                    <div data-i18n="Dropdowns">Dropdowns</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="ui-footer.html" className="menu-link">
                                    <div data-i18n="Footer">Footer</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="ui-list-groups.html" className="menu-link">
                                    <div data-i18n="List Groups">List groups</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="ui-modals.html" className="menu-link">
                                    <div data-i18n="Modals">Modals</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="ui-navbar.html" className="menu-link">
                                    <div data-i18n="Navbar">Navbar</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="ui-offcanvas.html" className="menu-link">
                                    <div data-i18n="Offcanvas">Offcanvas</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="ui-pagination-breadcrumbs.html" className="menu-link">
                                    <div data-i18n="Pagination &amp; Breadcrumbs">Pagination &amp; Breadcrumbs</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="ui-progress.html" className="menu-link">
                                    <div data-i18n="Progress">Progress</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="ui-spinners.html" className="menu-link">
                                    <div data-i18n="Spinners">Spinners</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="ui-tabs-pills.html" className="menu-link">
                                    <div data-i18n="Tabs &amp; Pills">Tabs &amp; Pills</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="ui-toasts.html" className="menu-link">
                                    <div data-i18n="Toasts">Toasts</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="ui-tooltips-popovers.html" className="menu-link">
                                    <div data-i18n="Tooltips & Popovers">Tooltips &amp; popovers</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="ui-typography.html" className="menu-link">
                                    <div data-i18n="Typography">Typography</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    {/* <!-- Extended components --> */}
                    <li className="menu-item">
                        <a href="javascript:void(0)" className="menu-link menu-toggle">
                            <i className="menu-icon tf-icons bx bx-copy"></i>
                            <div data-i18n="Extended UI">Extended UI</div>
                        </a>
                        <ul className="menu-sub">
                            <li className="menu-item">
                                <a href="extended-ui-perfect-scrollbar.html" className="menu-link">
                                    <div data-i18n="Perfect Scrollbar">Perfect scrollbar</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="extended-ui-text-divider.html" className="menu-link">
                                    <div data-i18n="Text Divider">Text Divider</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li className="menu-item">
                        <a href="icons-boxicons.html" className="menu-link">
                            <i className="menu-icon tf-icons bx bx-crown"></i>
                            <div data-i18n="Boxicons">Boxicons</div>
                        </a>
                    </li>

                    {/* <!-- Forms & Tables --> */}
                    <li className="menu-header small text-uppercase"><span className="menu-header-text">Forms &amp; Tables</span></li>
                    {/* <!-- Forms --> */}
                    <li className="menu-item">
                        <a href="javascript:void(0);" className="menu-link menu-toggle">
                            <i className="menu-icon tf-icons bx bx-detail"></i>
                            <div data-i18n="Form Elements">Form Elements</div>
                        </a>
                        <ul className="menu-sub">
                            <li className="menu-item">
                                <a href="forms-basic-inputs.html" className="menu-link">
                                    <div data-i18n="Basic Inputs">Basic Inputs</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="forms-input-groups.html" className="menu-link">
                                    <div data-i18n="Input groups">Input groups</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li className="menu-item">
                        <a href="javascript:void(0);" className="menu-link menu-toggle">
                            <i className="menu-icon tf-icons bx bx-detail"></i>
                            <div data-i18n="Form Layouts">Form Layouts</div>
                        </a>
                        <ul className="menu-sub">
                            <li className="menu-item">
                                <a href="form-layouts-vertical.html" className="menu-link">
                                    <div data-i18n="Vertical Form">Vertical Form</div>
                                </a>
                            </li>
                            <li className="menu-item">
                                <a href="form-layouts-horizontal.html" className="menu-link">
                                    <div data-i18n="Horizontal Form">Horizontal Form</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    {/* <!-- Tables --> */}
                    <li className="menu-item">
                        <a href="tables-basic.html" className="menu-link">
                            <i className="menu-icon tf-icons bx bx-table"></i>
                            <div data-i18n="Tables">Tables</div>
                        </a>
                    </li>
                    {/* <!-- Misc --> */}
                    <li className="menu-header small text-uppercase"><span className="menu-header-text">Misc</span></li>
                    <li className="menu-item">
                        <a
                            href="https://github.com/themeselection/sneat-html-admin-template-free/issues"
                            target="_blank"
                            className="menu-link"
                        >
                            <i className="menu-icon tf-icons bx bx-support"></i>
                            <div data-i18n="Support">Support</div>
                        </a>
                    </li>
                    <li className="menu-item">
                        <a
                            href="https://themeselection.com/demo/sneat-bootstrap-html-admin-template/documentation/"
                            target="_blank"
                            className="menu-link"
                        >
                            <i className="menu-icon tf-icons bx bx-file"></i>
                            <div data-i18n="Documentation">Documentation</div>
                        </a>
                    </li>
                </ul>
            </aside>
        </>
    )
}

export default Sidebar;