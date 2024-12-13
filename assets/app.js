// Les imports importants
import React from 'react';
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

const App = () => {
    return <HashRouter>
    <Navbar />
    <main className="container p-3">
        <Routes>
            <Route path="/invoices" element={<InvoicesPage />} />
            <Route path="/customers" element={<CustomersPage/>} />
            <Route path="/" element={<HomePage />} />
        </Routes>
    </main>
    </HashRouter>;
}

const container = document.querySelector('#app');
const root = createRoot(container);
root.render(<App />);