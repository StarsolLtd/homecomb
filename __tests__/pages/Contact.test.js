import React from 'react';
import {shallow} from 'enzyme';
import Contact from "../../assets/js/pages/Contact";
import ContactForm from "../../assets/js/components/ContactForm";

it("renders without crashing", () => {
    shallow(<Contact />);
});

it("renders a ContactForm", () => {
    const wrapper = shallow(<Contact />);
    const contactForm = <ContactForm />;
    expect(wrapper.contains(contactForm)).toEqual(true);
});
