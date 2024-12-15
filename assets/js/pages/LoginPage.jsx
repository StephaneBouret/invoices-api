import React, { useContext, useState } from 'react';
import { useNavigate } from "react-router-dom";
import { toast } from 'react-toastify';
import AuthContext from "../contexts/AuthContext";
import AuthAPI from '../services/authAPI.js';
import Field from '../components/forms/Field';

const LoginPage = () => {

    const { setIsAuthenticated } = useContext(AuthContext);

    const navigate = useNavigate();

    const [credentials, setCredentials] = useState({
        username: "",
        password: ""
    });
    const [error, setError] = useState("");

    // Gestion des champs
    const handleChange = ({currentTarget}) => {
        const { value, name } = currentTarget;
        // const value = event.currentTarget.value;
        // const name = event.currentTarget.name;
        setCredentials({...credentials, [name]: value});
    }

    // Gestion du submit
    const handleSubmit = async event => {
        event.preventDefault();

        try {
            await AuthAPI.authenticate(credentials);
            setError("");
            setIsAuthenticated(true);
            toast.success("Vous êtes désormais connecté !");
            navigate("/customers")
        } catch (error) {
            setError(
                "Aucun compte ne possède cette adresse email ou alors les informations ne correspondent pas !"
            );
            toast.error("Une erreur est survenue");
            // console.log(error.response);
        }
    }

    return ( 
        <>
        <h1>Connexion à l'application</h1>

        <form onSubmit={handleSubmit}>
            <Field  
                label="Adresse email"
                name="username"
                value={credentials.username}
                onChange={handleChange}
                placeholder="Adresse email de connexion"
                error={error}
            />
            <Field 
                name="password"
                label="Mot de passe"
                value={credentials.password}
                onChange={handleChange}
                type="password"
                error=""
            />
            <div className="form-group mt-4">
                <button type="submit" className="btn btn-success">
                    Je me connecte
                </button>
            </div>
        </form>
        </>
     );
}
 
export default LoginPage;