import { Navbar, Nav, Container, Button, Badge } from 'react-bootstrap';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { useCart } from '../context/CartContext';
import { ShoppingCart, LogOut, User } from 'lucide-react';

const Navigation = () => {
    const { isAuthenticated, logout, customerId, role } = useAuth();
    const { cartCount } = useCart();
    const navigate = useNavigate();

    const handleLogout = () => {
        logout();
        navigate('/login');
    };

    return (
        <Navbar bg="dark" variant="dark" expand="lg" className="mb-4">
            <Container fluid className="px-4">
                <Navbar.Brand as={Link as any} to="/">Mi Tienda</Navbar.Brand>
                <Navbar.Toggle aria-controls="basic-navbar-nav" />
                <Navbar.Collapse id="basic-navbar-nav">
                    <Nav className="me-auto">
                        <Nav.Link as={Link as any} to="/products">Productos</Nav.Link>
                        {isAuthenticated && role !== 'admin' && (
                            <Nav.Link as={Link as any} to="/orders">Mis Pedidos</Nav.Link>
                        )}
                        {isAuthenticated && role === 'admin' && (
                            <Nav.Link as={Link as any} to="/admin" className="fw-bold text-info">Panel Admin</Nav.Link>
                        )}
                    </Nav>
                    <Nav>
                        {isAuthenticated ? (
                            <>
                                <Navbar.Text className="me-3 d-flex align-items-center">
                                    <User size={18} className="me-1" />
                                    {customerId} <span className="badge bg-secondary ms-1">{role}</span>
                                </Navbar.Text>
                                {isAuthenticated && role !== 'admin' && (
                                    <Nav.Link as={Link as any} to="/cart" className="d-flex align-items-center me-3">
                                        <ShoppingCart size={20} className="me-1" />
                                        Carrito
                                        {cartCount > 0 && (
                                            <Badge bg="primary" pill className="ms-1">
                                                {cartCount}
                                            </Badge>
                                        )}
                                    </Nav.Link>
                                )}
                                <Button variant="outline-light" size="sm" onClick={handleLogout} className="d-flex align-items-center">
                                    <LogOut size={16} className="me-1" />
                                    Salir
                                </Button>
                            </>
                        ) : (
                            <Button as={Link as any} to="/login" variant="primary" size="sm">Iniciar Sesión</Button>
                        )}
                    </Nav>
                </Navbar.Collapse>
            </Container>
        </Navbar>
    );
};

export default Navigation;
