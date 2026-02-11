import React, { useState } from 'react';
import { Container, Row, Col, Card, Form, Button, Alert } from 'react-bootstrap';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import api from '../api/api';

const LoginView = () => {
    const [customerId, setCustomerId] = useState('');
    const [role, setRole] = useState('customer');
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

    const { login } = useAuth();
    const navigate = useNavigate();

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError('');
        setLoading(true);

        try {
            // Calling the backend login endpoint
            const response = await api.post('/api/login', { customerId, role });

            if (response.status === 200) {
                login(customerId, role);
                navigate('/products');
            }
        } catch (err: any) {
            setError(err.response?.data?.message || 'Error al iniciar sesión. Verifique los datos.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <Container className="mt-5">
            <Row className="justify-content-center">
                <Col md={5}>
                    <Card className="shadow-sm">
                        <Card.Header className="bg-primary text-white text-center py-3">
                            <h4>Iniciar Sesión</h4>
                        </Card.Header>
                        <Card.Body className="p-4">
                            {error && <Alert variant="danger">{error}</Alert>}
                            <Form onSubmit={handleSubmit}>
                                <Form.Group className="mb-3">
                                    <Form.Label>ID de Cliente</Form.Label>
                                    <Form.Control
                                        type="text"
                                        placeholder="Ej: user123"
                                        value={customerId}
                                        onChange={(e) => setCustomerId(e.target.value)}
                                        required
                                    />
                                </Form.Group>

                                <Form.Group className="mb-4">
                                    <Form.Label>Rol</Form.Label>
                                    <Form.Select
                                        value={role}
                                        onChange={(e) => setRole(e.target.value)}
                                    >
                                        <option value="customer">Cliente</option>
                                        <option value="admin">Administrador</option>
                                    </Form.Select>
                                </Form.Group>

                                <div className="d-grid">
                                    <Button variant="primary" type="submit" disabled={loading}>
                                        {loading ? 'Entrando...' : 'Entrar'}
                                    </Button>
                                </div>
                            </Form>
                        </Card.Body>
                        <Card.Footer className="text-muted text-center small">
                            Simulación de autenticación para prueba técnica
                        </Card.Footer>
                    </Card>
                </Col>
            </Row>
        </Container>
    );
};

export default LoginView;
