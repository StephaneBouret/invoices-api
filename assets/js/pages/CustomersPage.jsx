import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { toast } from 'react-toastify';
import TableLoader from '../components/loaders/TableLoader';
import Pagination from '../components/Pagination';
import CustomersAPI from '../services/customersAPI.js';

const CustomersPage = () => {

    const [customers, setCustomers] = useState([]);
    // Création d'un state pour la pagination (page par défaut 1)
    const [currentPage, setCurrentPage] = useState(1);
    // Création du state pour la recherche
    const [search, setSearch] = useState("");
    const [loading, setLoading] = useState(true);

    // Permet d'aller récupérer les customers car React interdit de passer une async dans un useEffect
    const fetchCustomers = async () => {
        try {
            const data = await CustomersAPI.findAll();
            setCustomers(data);
            setLoading(false);
         } catch (error) {
            // console.log(error.response);
            toast.error("Impossible de charger les clients");
        }
    }

    // Au chargement du composant, on va chercher les customers
    useEffect(() => {
        // axios
        //     .get("https://127.0.0.1:8000/api/customers")
        //     .then(response => response.data['member'])
        // 2. refactorisation vers services
        // CustomersAPI.findAll()
        //     .then(data => setCustomers(data))
        //     .catch(error => console.log(error.response));
        fetchCustomers();
    }, [])

    // Gestion de la suppression d'un customer - ajout async car try et catch !
    const handleDelete = async id => {
        // console.log(id);
    
        // Copie du tableau des customers avant la suppression
        const originalCustomers = [...customers];
    
        // 1. L'approche optimiste
        setCustomers(customers.filter(customer => customer.id !== id));
        // 2. L'approche pessimiste
        // Deuxième façon de faire une requête (traitement d'une promesse)
        // CustomersAPI.delete(id)
        //     .then(response => console.log("OK"))
        //     .catch(error => {
        //         setCustomers(originalCustomers);
        //         console.log(error.response);
        //     });
        try {
            await CustomersAPI.delete(id);
            toast.success("Le client a bien été supprimé");
        } catch (error) {
            setCustomers(originalCustomers);
            toast.error("La suppression du client n'a pas pu fonctionner");
        }
    };

    // Gestion du changement de page
    const handlePageChange = page => {
        setCurrentPage(page);
    }

    // Gestion de la recherche
    const handleSearch = ({currentTarget}) => {
        setSearch(currentTarget.value);
        setCurrentPage(1);
    }

    const itemsPerPage = 10;

    // Filtrage des customers en fonction de la recherche
    const filteredCustomers = customers.filter(
        c => 
            c.firstName.toLowerCase().includes(search.toLowerCase()) ||
            c.lastName.toLowerCase().includes(search.toLowerCase()) ||
            c.email.toLowerCase().includes(search.toLowerCase()) ||
            (c.company && c.company.toLowerCase().includes(search.toLowerCase()))
    );

    // D'où on part (start) pendant combien (itemsPerPage)
    // const start = currentPage * itemsPerPage - itemsPerPage;
    //            4           * 10           - 10 = 30
    // const paginatedCustomers = customers.slice(start, start + itemsPerPage);
    const paginatedCustomers = Pagination.getData(
        filteredCustomers, 
        currentPage, 
        itemsPerPage
    );

    return (     <>
        <div className="mb-3 d-flex justify-content-between align-items-center">
            <h1>Liste des clients</h1>
            <Link to="/customers/new" className="btn btn-primary">
                Créer un client
            </Link>
        </div>

        <div className="form-group">
            <input
            type="text"
            onChange={handleSearch}
            value={search}
            className="form-control"
            placeholder="Rechercher ..."
            />
        </div>
    
        <table className="table table-hover">
            <thead>
                <tr>
                    <th>Id.</th>
                    <th>Client</th>
                    <th>Email</th>
                    <th>Entreprise</th>
                    <th className='text-center'>Factures</th>
                    <th className='text-center'>Montant total</th>
                    <th></th>
                </tr>
            </thead>
            {!loading && (
            <tbody>
                {paginatedCustomers.map(customer => (
                    <tr key={customer.id}>
                        <td>{customer.id}</td>
                        <td>
                            <Link to={"/customers/" + customer.id}>{customer.firstName} {customer.lastName}</Link>
                        </td>
                        <td>{customer.email}</td>
                        <td>{customer.company}</td>
                        <td className='text-center'>
                        <span className="badge bg-primary">{customer.invoices.length}</span>
                        </td>
                        <td className='text-center'>{customer.totalAmount.toLocaleString()} €</td>
                        <td>
                            <button 
                            onClick={() => handleDelete(customer.id)}
                            className="btn btn-sm btn-danger"
                            disabled={customer.invoices.length > 0}
                            >Supprimer</button>
                        </td>
                    </tr>
                ))}
            </tbody>
            )}
        </table>
        {loading && <TableLoader />}

        {itemsPerPage < filteredCustomers.length && 
        (<Pagination 
            currentPage={currentPage} 
            itemsPerPage={itemsPerPage} 
            length={filteredCustomers.length} 
            onPageChanged={handlePageChange} 
        />
        )}

        </> );
}
 
export default CustomersPage;