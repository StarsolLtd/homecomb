import React from "react";
import {Link} from "react-router-dom";

function Footer() {
    return (
        <footer className="mt-auto navbar navbar-expand-sm">
            <ul className="list-inline text-center">
                <li className="list-inline-item"><a href="/" className="nav-link">Search</a></li>
                <li className="list-inline-item"><Link to="/about" className="nav-link">About</Link></li>
                <li className="list-inline-item"><Link to="/contact" className="nav-link">Contact Us</Link></li>
                <li className="list-inline-item"><a href="/privacy-policy" className="nav-link">Privacy Policy</a></li>
            </ul>
        </footer>
    );
}

export default Footer;