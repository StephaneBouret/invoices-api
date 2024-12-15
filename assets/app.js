// Les imports importants
import React, { useState } from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter, HashRouter, Routes, Route } from 'react-router';

/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import Navbar from './js/components/Navbar';
import HomePage from './js/pages/HomePage';
import CustomersPage from './js/pages/CustomersPage';
import InvoicesPage from './js/pages/InvoicesPage';
import InvoicePage from './js/pages/InvoicePage';
import LoginPage from './js/pages/LoginPage';
import AuthAPI from './js/services/authAPI';
import AuthContext from './js/contexts/AuthContext';
import PrivateRoute from './js/components/PrivateRoute';
import CustomerPage from './js/pages/CustomerPage';
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import RegisterPage from './js/pages/RegisterPage';

AuthAPI.setup();

const App = () => {
    // TODO : il faudrait par défaut que l'on demande à notre AuthAPI si on est connecté ou pas 
    const [isAuthenticated, setIsAuthenticated] = useState(
        AuthAPI.isAuthenticated()
    );

    return (
        <AuthContext.Provider 
            value={{
                isAuthenticated,
                setIsAuthenticated
            }}
            >            
            <HashRouter>
                <Navbar />
                    <main className="container p-3">
                        <Routes>
                            <Route path="/login" element={<LoginPage />} />
                            <Route path='/register' element={<RegisterPage />} />
                            {/* <Route path="/invoices" element={<InvoicesPage />} /> */}
                            <Route element={<PrivateRoute />}>
                                <Route path="/invoices/:id" element={<InvoicePage />} />
                            </Route>
                            <Route element={<PrivateRoute />}>
                                <Route path="/invoices" element={<InvoicesPage />} />
                            </Route>
                            {/* <Route path="/customers" element={<CustomersPage/>} /> */}
                            <Route element={<PrivateRoute />}>
                                <Route path="/customers/:id" element={<CustomerPage />} />
                            </Route>
                            <Route element={<PrivateRoute />}>
                                <Route path="/customers" element={<CustomersPage />} />
                            </Route>
                            <Route path="/" element={<HomePage />} />
                        </Routes>
                    </main>
            </HashRouter>
            <ToastContainer 
                position="bottom-left"
            />
        </AuthContext.Provider>
    );
}

const container = document.querySelector('#app');
const root = createRoot(container);
root.render(<App />);