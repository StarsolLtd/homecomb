import React from "react";
import {Link} from "react-router-dom";

const Footer = (props) => {
    return (
        <footer className="mt-auto navbar navbar-expand-sm">
            <ul className="list-inline text-center">
                <li className="list-inline-item"><Link to="/" className="nav-link">Search</Link></li>
                {props.user &&
                    <li className="list-inline-item"><a href="/logout" className="nav-link">Log Out</a></li>
                }
                {!props.user &&
                    <li className="list-inline-item"><a href="/login" className="nav-link">Log In</a></li>
                }
                <li className="list-inline-item"><Link to="/about" className="nav-link">About</Link></li>
                <li className="list-inline-item"><Link to="/contact" className="nav-link">Contact Us</Link></li>
                <li className="list-inline-item"><Link to="/privacy-policy" className="nav-link">Privacy Policy</Link></li>
            </ul>
        </footer>
    );
}

export default Footer;