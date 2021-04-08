import React from 'react';

const TextLogo = (props) => {
    const textLogoClasses = `logo ${props.className}`;
    return (
        <span className={textLogoClasses}>
            <span className="logo-first">home</span><span className="logo-second">comb</span>
        </span>
    );
}

export default TextLogo;
