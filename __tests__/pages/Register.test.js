import React from 'react'
import { shallow } from 'enzyme'
import Register from '../../assets/js/pages/Register'
import RegisterForm from '../../assets/js/components/RegisterForm'

it('renders without crashing', () => {
  shallow(<Register />)
})

it('renders a RegisterForm', () => {
  const wrapper = shallow(<Register />)
  const registerForm = <RegisterForm />
  expect(wrapper.contains(registerForm)).toEqual(true)
})
