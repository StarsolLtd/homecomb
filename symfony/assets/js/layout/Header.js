import React from "react";
import {Link} from "react-router-dom";

function Header() {
    return (
        <nav className="navbar navbar-dark">
            <div className="container">
                <span className="navbar-brand logo-medium">
                    <Link to="/">
                        <span className="red">Home</span><span className="bronze">Comb</span>
                    </Link>
                </span>
            </div>
        </nav>
    );
}

export default Header;