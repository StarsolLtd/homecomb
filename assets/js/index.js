import React from 'react'
import ReactDOM from 'react-dom'
import { BrowserRouter } from "react-router-dom"
import Front from "./Front";

import '../styles/app.scss';

ReactDOM.render(
    <BrowserRouter>
        <Front />
    </BrowserRouter>,
    document.getElementById('root')
);