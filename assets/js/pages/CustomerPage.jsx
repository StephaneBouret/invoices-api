import React, { useEffect, useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import { toast } from 'react-toastify';
import Field from '../components/forms/Field';
import FormContentLoader from '../components/loaders/FormContentLoader';
import CustomersAPI from '../services/customersAPI.js';

const CustomerPage = () => {
    const navigate = useNavigate();
    const params = useParams();

    const { id = "new" } = params;
    // if (id !== "new") {
    //     console.log(id);
    // }

    const [customer, setCustomer] = useState({
        lastName: "",
        firstName: "",
        email: "",
        company: ""
    })

    const [errors, setErrors] = useState({
        lastName: "",
        firstName: "",
        email: "",
        company: ""
    });
    const [loading, setLoading] = useState(false);
    const [editing, setEditing] = useState(false);

    // Récupération du customer en fonction de l'identifiant
    const fetchCustomer = async id => {
        try {
            const { firstName, lastName, email, company } = await CustomersAPI.find(
                id
            );  
            // console.log(data);           
            setCustomer({ firstName, lastName, email, company });
            setLoading(false);
        } catch (error) {
            // console.log(error.response);
            toast.error("Le client n'a pas pu être chargé");
            navigate("/customers");
        }
    }

    // Chargement du customer si besoin au chargement du composant ou au changement de l'identifiant
    useEffect(() => {
        if (id !== "new") {
            setLoading(true);
            setEditing(true);
            fetchCustomer(id);
        }
    }, [id]);

    // Gestion de la soumission du formulaire
    const handleSubmit = async event => {
        event.preventDefault();
        // console.log(customer);
        try {
            setErrors({});
            
            if (editing) {
                await CustomersAPI.update(id, customer);
                toast.success("Le client a bien été modifié");
                // console.log(response.data);
            } else {
                await CustomersAPI.create(customer);
                toast.success("Le client a bien été créé");
                navigate("/customers");
            }
            // console.log(response.data);
        } catch ({ response }) {
            const { violations } = response.data;
            // console.log(error.response);
            if (violations) {
                const apiErrors = {};
                violations.forEach(({ propertyPath, message }) => {
                    apiErrors[propertyPath] = message;
                    // console.log(apiErrors);
                    setErrors(apiErrors);
                    toast.error("Des erreurs dans votre formulaire !");
                })
            }
        }
    }

    // Gestion des changements des inputs dans le formulaire
    function handleChange({ currentTarget }) {
        const { name, value } = currentTarget;
        setCustomer({ ...customer, [name]: value });
    }

    return ( 
        <>
        {(!editing && <h1>Création d'un client</h1>) || (
            <h1>Modification du client</h1>
        )}
        {loading && <FormContentLoader />}
        {!loading && (
        <form onSubmit={handleSubmit}>
            <Field
                name="lastName"
                label="Nom de famille"
                placeholder="Nom de famille du client"
                value={customer.lastName}
                onChange={handleChange}
                error={errors.lastName}
            />
            <Field
                name="firstName"
                label="Prénom"
                placeholder="Prénom du client"
                value={customer.firstName}
                onChange={handleChange}
                error={errors.firstName}
            />
            <Field
                name="email"
                label="Email"
                placeholder="Adresse email du client"
                type="email"
                value={customer.email}
                onChange={handleChange}
                error={errors.email}
            />
            <Field
                name="company"
                label="Entreprise"
                placeholder="Entreprise du client"
                value={customer.company}
                onChange={handleChange}
                error={errors.company}
          />
            <div className="form-group mt-3">
                <button type="submit" className="btn btn-success">
                Enregistrer
                </button>
                <Link to="/customers" className="btn btn-link">
                Retour à la liste
                </Link>
          </div>
        </form>
        )}
        </>
     );
}
 
export default CustomerPage;