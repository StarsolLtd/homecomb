import React from 'react'
import ReactDOM from 'react-dom'
import { BrowserRouter } from "react-router-dom"

import '../../../styles/app.scss';
import Router from "./Router";

ReactDOM.render(
    <BrowserRouter>
        <Router />
    </BrowserRouter>,
    document.getElementById('root')
);